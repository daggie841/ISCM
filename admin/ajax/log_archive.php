<?php
require __DIR__ . '/../../core/bootstrap.php';
guard_ajax('admin', ['admin']);
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_fail('Invalid request.', 405);

$name = basename(request_input()['file'] ?? '');
$path = ROOT . '/logs/' . $name;
if (!preg_match('/^[a-z_]+\.log$/', $name) || !is_file($path)) json_fail('Log file not found.', 404);

$dir = ROOT . '/logs/archive';
if (!is_dir($dir)) mkdir($dir, 0775, true);
$dest = $dir . '/' . substr($name, 0, -4) . '_' . date('Ymd_His') . '.log';
if (!copy($path, $dest) || file_put_contents($path, '') === false) { log_error('admin', "Archive failed for $name"); json_fail('Could not archive the log.', 500); }
log_activity('log_archived', $name);
json_ok(['message' => 'Log archived and cleared.']);
