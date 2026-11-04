<?php
require __DIR__ . '/../../core/bootstrap.php';
guard('admin', ['admin']);
$id = (int)($_GET['id'] ?? 0);
$p = $id ? db_one('SELECT * FROM products WHERE id = ?', [$id]) : null;
if ($id && !$p) { http_response_code(404); echo 'Product not found.'; exit; }
$cats = db_all('SELECT id, name FROM categories ORDER BY name');
$sups = db_all("SELECT id, name FROM suppliers WHERE status = 'active' ORDER BY name");
$locs = db_all('SELECT l.id, l.name, l.status, COALESCE(s.quantity, 0) AS qty FROM locations l LEFT JOIN stock s ON s.location_id = l.id AND s.product_id = ? ORDER BY l.name', [$id]);
page_header($p ? 'Edit product' : 'Add product', 'Products');
?>
<form id="product-form">
  <input type="hidden" name="id" value="<?= (int)($p['id'] ?? 0) ?>">
  <div class="card">
    <h2>Product details</h2>
    <div class="row">
      <div><label for="sku">SKU</label><input id="sku" name="sku" required maxlength="40" value="<?= e($p['sku'] ?? '') ?>"></div>
      <div><label for="barcode">Barcode (scan or type)</label><input id="barcode" name="barcode" maxlength="60" value="<?= e($p['barcode'] ?? '') ?>"></div>
    </div>
    <label for="name">Product name</label>
    <input id="name" name="name" required maxlength="150" value="<?= e($p['name'] ?? '') ?>">
    <div class="row">
      <div><label for="category_id">Category</label>
        <select id="category_id" name="category_id"><option value="">None</option>
          <?php foreach ($cats as $c): ?><option value="<?= (int)$c['id'] ?>" <?= ($p && (int)$p['category_id'] === (int)$c['id']) ? 'selected' : '' ?>><?= e($c['name']) ?></option><?php endforeach; ?>
        </select></div>
      <div><label for="supplier_id">Main supplier</label>
        <select id="supplier_id" name="supplier_id"><option value="">None</option>
          <?php foreach ($sups as $s): ?><option value="<?= (int)$s['id'] ?>" <?= ($p && (int)$p['supplier_id'] === (int)$s['id']) ? 'selected' : '' ?>><?= e($s['name']) ?></option><?php endforeach; ?>
        </select></div>
    </div>
    <div class="row">
      <div><label for="unit_cost">Unit cost</label><input id="unit_cost" name="unit_cost" type="number" step="0.01" min="0" required value="<?= e($p['unit_cost'] ?? '0') ?>"></div>
      <div><label for="unit_price">Selling price</label><input id="unit_price" name="unit_price" type="number" step="0.01" min="0" required value="<?= e($p['unit_price'] ?? '0') ?>"></div>
    </div>
    <div class="row">
      <div><label for="reorder_level">Reorder level (units)</label><input id="reorder_level" name="reorder_level" type="number" min="0" required value="<?= e($p['reorder_level'] ?? '10') ?>"></div>
      <div><label for="safety_stock">Safety stock (units)</label><input id="safety_stock" name="safety_stock" type="number" min="0" required value="<?= e($p['safety_stock'] ?? '5') ?>"></div>
      <div><label for="status">Status</label>
        <select id="status" name="status"><option value="active" <?= ($p['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active</option><option value="inactive" <?= ($p['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option></select></div>
    </div>
  </div>

  <div class="card">
    <h2><?= $p ? 'Stock by location' : 'Opening stock by location' ?></h2>
    <div class="tablewrap"><table>
      <thead><tr><th>Location</th><th style="width:180px">Quantity</th></tr></thead>
      <tbody>
        <?php foreach ($locs as $l): ?>
          <tr><td><?= e($l['name']) ?><?= $l['status'] === 'inactive' ? ' (inactive)' : '' ?></td>
          <td><input type="number" min="0" data-loc="<?= (int)$l['id'] ?>" value="<?= (int)$l['qty'] ?>" aria-label="Quantity at <?= e($l['name']) ?>"></td></tr>
        <?php endforeach; ?>
      </tbody>
    </table></div>
    <?php if ($p): ?>
      <label for="reason">Reason for stock changes (saved in the movement history)</label>
      <input id="reason" name="reason" maxlength="200" placeholder="e.g. Stock count correction">
    <?php endif; ?>
  </div>

  <div class="actions">
    <button class="btn" type="submit">Save product</button>
    <a class="btn ghost" href="products.php">Cancel</a>
  </div>
</form>
<?php page_footer(['admin/js/product_form.js']);
