<?php
require __DIR__ . '/../../core/bootstrap.php';
guard_ajax('admin', ['admin']);
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_fail('Invalid request.', 405);

$id = (int)(request_input()['id'] ?? 0);
$p = db_one('SELECT id, sku, status FROM products WHERE id = ?', [$id]);
if (!$p) json_fail('Product not found.', 404);

$new = $p['status'] === 'active' ? 'inactive' : 'active';
db_query('UPDATE products SET status = ? WHERE id = ?', [$new, $id]);
log_activity('product_' . $new, $p['sku']);
json_ok(['status' => $new, 'message' => 'Product ' . ($new === 'active' ? 'activated' : 'deactivated') . '.']);
