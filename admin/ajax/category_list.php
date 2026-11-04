<?php
require __DIR__ . '/../../core/bootstrap.php';
guard_ajax('admin', ['admin']);
json_ok(['categories' => db_all('SELECT c.id, c.name, COUNT(p.id) AS products FROM categories c LEFT JOIN products p ON p.category_id = c.id GROUP BY c.id ORDER BY c.name')]);
