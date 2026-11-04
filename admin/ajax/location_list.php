<?php
require __DIR__ . '/../../core/bootstrap.php';
guard_ajax('admin', ['admin']);
json_ok(['locations' => db_all('SELECT id, name, address, status FROM locations ORDER BY name')]);
