<?php
require __DIR__ . '/../../core/bootstrap.php';
require ROOT . '/core/models/product.php';
guard_ajax('warehouse', ['warehouse', 'admin']);

$thr = (int)setting('low_stock_threshold', 10);
$mult = max(1, (int)setting('overstock_multiplier', 3));
$lvl = "IF(t.reorder_level > 0, t.reorder_level, $thr)";

$base = "SELECT p.id, p.sku, p.name, p.reorder_level, c.name AS category,
                COALESCE(SUM(st.quantity), 0) AS total_qty
         FROM products p
         LEFT JOIN categories c ON c.id = p.category_id
         LEFT JOIN stock st ON st.product_id = p.id
         WHERE p.status = 'active'
         GROUP BY p.id";

$counts = db_one("SELECT
    COALESCE(SUM(CASE WHEN total_qty <= 0 THEN 1 ELSE 0 END), 0) AS out_qty,
    COALESCE(SUM(CASE WHEN total_qty > 0 AND total_qty <= $lvl THEN 1 ELSE 0 END), 0) AS low_qty,
    COALESCE(SUM(CASE WHEN total_qty > $lvl * $mult THEN 1 ELSE 0 END), 0) AS over_qty
  FROM ($base) t");

$today = "created_at >= CURDATE() AND created_at < CURDATE() + INTERVAL 1 DAY";
$received = (int)db_val("SELECT COALESCE(SUM(quantity), 0) FROM stock_movements
                         WHERE type = 'receipt' AND quantity > 0 AND $today");
$issued = (int)db_val("SELECT COALESCE(SUM(-quantity), 0) FROM stock_movements
                       WHERE type = 'issue' AND quantity < 0 AND $today");

$lowest = db_all("$base ORDER BY total_qty ASC, p.name ASC LIMIT 10");
foreach ($lowest as &$row) $row['stock_status'] = product_stock_status((int)$row['total_qty'], $row['reorder_level']);
unset($row);

json_ok([
    'stats' => [
        'low' => (int)$counts['low_qty'],
        'out' => (int)$counts['out_qty'],
        'overstock' => (int)$counts['over_qty'],
        'received_today' => $received,
        'issued_today' => $issued,
    ],
    'lowest' => $lowest,
]);
