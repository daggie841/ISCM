<?php
// Shared product rules, used by every department that edits products.

function product_stock_status($qty, $reorder_level) {
    $level = (int)$reorder_level > 0 ? (int)$reorder_level : (int)setting('low_stock_threshold', 10);
    $mult = max(1, (int)setting('overstock_multiplier', 3));
    if ($qty <= 0) return 'out';
    if ($qty <= $level) return 'low';
    if ($qty > $level * $mult) return 'overstock';
    return 'ok';
}

// Validates form input. Returns [clean array, error message or null].
function product_clean($in, $id = 0) {
    $sku = strtoupper(clean_str($in['sku'] ?? '', 40));
    $name = clean_str($in['name'] ?? '', 150);
    $barcode = clean_str($in['barcode'] ?? '', 60);
    if (!preg_match('/^[A-Z0-9._-]{2,40}$/', $sku)) return [null, 'SKU must be 2-40 letters, numbers, dots, dashes or underscores.'];
    if ($name === '') return [null, 'Enter the product name.'];
    foreach (['unit_cost' => 'Cost', 'unit_price' => 'Price'] as $k => $label) {
        if (!is_numeric($in[$k] ?? '') || (float)$in[$k] < 0 || (float)$in[$k] > 99999999) return [null, "$label must be a number, 0 or more."];
    }
    foreach (['reorder_level' => 'Reorder level', 'safety_stock' => 'Safety stock'] as $k => $label) {
        if (!ctype_digit((string)($in[$k] ?? '')) || (int)$in[$k] > 1000000) return [null, "$label must be a whole number, 0 or more."];
    }
    $cat = (int)($in['category_id'] ?? 0);
    $sup = (int)($in['supplier_id'] ?? 0);
    if ($cat && !db_one('SELECT id FROM categories WHERE id = ?', [$cat])) return [null, 'Choose a valid category.'];
    if ($sup && !db_one('SELECT id FROM suppliers WHERE id = ?', [$sup])) return [null, 'Choose a valid supplier.'];
    if (db_one('SELECT id FROM products WHERE sku = ? AND id <> ?', [$sku, $id])) return [null, 'Another product already uses that SKU.'];
    if ($barcode !== '' && db_one('SELECT id FROM products WHERE barcode = ? AND id <> ?', [$barcode, $id])) return [null, 'Another product already uses that barcode.'];
    return [[
        'sku' => $sku, 'barcode' => $barcode === '' ? null : $barcode, 'name' => $name,
        'category_id' => $cat ?: null, 'supplier_id' => $sup ?: null,
        'unit_cost' => round((float)$in['unit_cost'], 2), 'unit_price' => round((float)$in['unit_price'], 2),
        'reorder_level' => (int)$in['reorder_level'], 'safety_stock' => (int)$in['safety_stock'],
        'status' => ($in['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active',
    ], null];
}

// Sets the stock at one location and records the difference as a movement. Call inside a transaction.
function product_set_stock($product_id, $location_id, $qty, $reason, $user_id) {
    $cur = db_val('SELECT quantity FROM stock WHERE product_id = ? AND location_id = ? FOR UPDATE', [$product_id, $location_id]);
    $cur = $cur === null ? 0 : (int)$cur;
    $delta = $qty - $cur;
    if ($delta === 0) return false;
    db_query('INSERT INTO stock (product_id, location_id, quantity) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE quantity = VALUES(quantity)',
        [$product_id, $location_id, $qty]);
    db_query("INSERT INTO stock_movements (product_id, location_id, type, quantity, reference, user_id, reason) VALUES (?, ?, 'adjustment', ?, 'product-form', ?, ?)",
        [$product_id, $location_id, $delta, $user_id, $reason]);
    return true;
}

// Shared product list query for admin and warehouse.
function product_search(array $filters) {
    $q = clean_str($filters['q'] ?? '', 100);
    $cat = (int)($filters['category'] ?? 0);
    $status = (string)($filters['status'] ?? '');
    $stock = (string)($filters['stock'] ?? '');
    $page = max(1, (int)($filters['page'] ?? 1));
    $per = (int)($filters['per'] ?? 15);
    if ($per < 1 || $per > 100) $per = 15;

    $where = ['1=1'];
    $p = [];
    if ($q !== '') { $where[] = '(p.name LIKE ? OR p.sku LIKE ? OR p.barcode LIKE ?)'; array_push($p, "%$q%", "%$q%", "%$q%"); }
    if ($cat) { $where[] = 'p.category_id = ?'; $p[] = $cat; }
    if (in_array($status, ['active', 'inactive'], true)) { $where[] = 'p.status = ?'; $p[] = $status; }

    $thr = (int)setting('low_stock_threshold', 10);
    $mult = max(1, (int)setting('overstock_multiplier', 3));
    $lvl = "IF(p.reorder_level > 0, p.reorder_level, $thr)";
    $having = [
        'out' => 'total_qty <= 0',
        'low' => "total_qty > 0 AND total_qty <= $lvl",
        'overstock' => "total_qty > $lvl * $mult",
        'ok' => "total_qty > $lvl AND total_qty <= $lvl * $mult",
    ][$stock] ?? '1=1';

    $inner = "SELECT p.id, p.sku, p.barcode, p.name, p.unit_cost, p.unit_price, p.reorder_level, p.status,
                     c.name AS category, s.name AS supplier, COALESCE(SUM(st.quantity), 0) AS total_qty
              FROM products p
              LEFT JOIN categories c ON c.id = p.category_id
              LEFT JOIN suppliers s ON s.id = p.supplier_id
              LEFT JOIN stock st ON st.product_id = p.id
              WHERE " . implode(' AND ', $where) . " GROUP BY p.id HAVING $having";

    $total = (int)db_val("SELECT COUNT(*) FROM ($inner) t", $p);
    $rows = db_all("$inner ORDER BY p.name LIMIT $per OFFSET " . (($page - 1) * $per), $p);
    foreach ($rows as &$row) $row['stock_status'] = product_stock_status((int)$row['total_qty'], $row['reorder_level']);
    unset($row);
    return ['products' => $rows, 'total' => $total, 'page' => $page, 'per' => $per];
}

// Saves details and stock changes in one transaction. Returns id, error, code and clean row.
function product_save(array $in, $id, $user_id, $section, $default_reason = 'Product edit') {
    $id = (int)$id;
    if ($id && !db_one('SELECT id FROM products WHERE id = ?', [$id])) {
        return ['id' => 0, 'error' => 'Product not found.', 'code' => 404, 'product' => null];
    }
    [$c, $err] = product_clean($in, $id);
    if ($err) return ['id' => 0, 'error' => $err, 'code' => 400, 'product' => null];

    $stock = [];
    foreach ((array)($in['stock'] ?? []) as $loc => $qty) {
        if (!ctype_digit((string)$loc) || !db_one('SELECT id FROM locations WHERE id = ?', [(int)$loc])) {
            return ['id' => 0, 'error' => 'Unknown location.', 'code' => 400, 'product' => null];
        }
        if ($qty === '' || $qty === null) continue;
        if (!ctype_digit((string)$qty) || (int)$qty > 10000000) {
            return ['id' => 0, 'error' => 'Stock quantities must be whole numbers, 0 or more.', 'code' => 400, 'product' => null];
        }
        $stock[(int)$loc] = (int)$qty;
    }

    $pdo = db();
    $pdo->beginTransaction();
    try {
        if ($id) {
            db_query('UPDATE products SET sku=?, barcode=?, name=?, category_id=?, unit_cost=?, unit_price=?, reorder_level=?, safety_stock=?, supplier_id=?, status=? WHERE id=?',
                [$c['sku'], $c['barcode'], $c['name'], $c['category_id'], $c['unit_cost'], $c['unit_price'], $c['reorder_level'], $c['safety_stock'], $c['supplier_id'], $c['status'], $id]);
            $reason = clean_str($in['reason'] ?? '', 200) ?: $default_reason;
        } else {
            $id = db_insert('INSERT INTO products (sku, barcode, name, category_id, unit_cost, unit_price, reorder_level, safety_stock, supplier_id, status) VALUES (?,?,?,?,?,?,?,?,?,?)',
                [$c['sku'], $c['barcode'], $c['name'], $c['category_id'], $c['unit_cost'], $c['unit_price'], $c['reorder_level'], $c['safety_stock'], $c['supplier_id'], $c['status']]);
            $reason = 'Opening stock';
        }
        foreach ($stock as $loc => $qty) product_set_stock($id, $loc, $qty, $reason, (int)$user_id);
        $pdo->commit();
        return ['id' => $id, 'error' => null, 'code' => 200, 'product' => $c];
    } catch (Throwable $ex) {
        $pdo->rollBack();
        log_error($section, 'product_save failed: ' . $ex->getMessage());
        return ['id' => 0, 'error' => 'Could not save the product. The error was logged.', 'code' => 500, 'product' => null];
    }
}
