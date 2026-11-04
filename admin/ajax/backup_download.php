<?php
require __DIR__ . '/../../core/bootstrap.php';
guard_ajax('admin', ['admin']);

$file = basename($_GET['file'] ?? '');
$path = ROOT . '/backups/' . $file;
if (!preg_match('/^backup_\d{8}_\d{6}\.sql$/', $file) || !is_file($path)) json_fail('Backup not found.', 404);

log_activity('backup_downloaded', $file);
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $file . '"');
header('Content-Length: ' . filesize($path));
readfile($path);
