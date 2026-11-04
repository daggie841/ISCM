<?php
require __DIR__ . '/../../core/bootstrap.php';
guard_ajax('admin', ['admin']);

$keys = require ROOT . '/core/models/setting_keys.php';
$out = [];
foreach ($keys as $k => $def) {
    $v = (string)setting($k, '');
    $out[$k] = ($def[1] && $v !== '') ? '********' : $v;
}
json_ok(['settings' => $out]);
