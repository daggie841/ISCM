<?php
require __DIR__ . '/../../core/bootstrap.php';
guard_ajax('admin', ['admin']);

$files = [];
foreach (glob(ROOT . '/logs/*.log') ?: [] as $f) {
    $files[] = ['name' => basename($f), 'size' => filesize($f), 'modified' => date('Y-m-d H:i', filemtime($f))];
}
json_ok(['files' => $files]);
