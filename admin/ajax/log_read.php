<?php
require __DIR__ . '/../../core/bootstrap.php';
guard_ajax('admin', ['admin']);

$name = basename($_GET['file'] ?? '');
$path = ROOT . '/logs/' . $name;
if (!preg_match('/^[a-z_]+\.log$/', $name) || !is_file($path)) json_fail('Log file not found.', 404);

$q = clean_str($_GET['q'] ?? '', 80);
$lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
if ($q !== '') $lines = array_values(array_filter($lines, fn($l) => stripos($l, $q) !== false));
json_ok(['lines' => array_slice(array_reverse($lines), 0, 200), 'total' => count($lines)]);
