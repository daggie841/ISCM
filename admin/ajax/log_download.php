<?php
require __DIR__ . '/../../core/bootstrap.php';
guard_ajax('admin', ['admin']);

$name = basename($_GET['file'] ?? '');
$path = ROOT . '/logs/' . $name;
if (!preg_match('/^[a-z_]+\.log$/', $name) || !is_file($path)) json_fail('Log file not found.', 404);

header('Content-Type: text/plain; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $name . '"');
readfile($path);
