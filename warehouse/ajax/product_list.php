<?php
require __DIR__ . '/../../core/bootstrap.php';
require ROOT . '/core/models/product.php';
guard_ajax('warehouse', ['warehouse', 'admin']);

$d = product_search($_GET);
$d['categories'] = db_all('SELECT id, name FROM categories ORDER BY name');
json_ok($d);
