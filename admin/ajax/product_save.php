<?php
require __DIR__ . '/../../core/bootstrap.php';
require ROOT . '/core/models/product.php';
guard_ajax('admin', ['admin']);
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_fail('Invalid request.', 405);

$in = request_input();
$r = product_save($in, (int)($in['id'] ?? 0), (int)$_SESSION['user_id'], 'admin', 'Edited by administrator');
if ($r['error']) json_fail($r['error'], $r['code']);

log_activity('product_saved', $r['product']['sku'] . ' ' . $r['product']['name']);
json_ok(['id' => $r['id'], 'message' => 'Product saved.']);
