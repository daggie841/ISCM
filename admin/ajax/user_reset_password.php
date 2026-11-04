<?php
require __DIR__ . '/../../core/bootstrap.php';
guard_ajax('admin', ['admin']);
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_fail('Invalid request.', 405);

$id = (int)(request_input()['id'] ?? 0);
$u = db_one("SELECT id, email FROM users WHERE id = ? AND status <> 'pending'", [$id]);
if (!$u) json_fail('User not found.', 404);

// Temporary password shown once to the admin; user must change it at next login.
$temp = substr(str_replace(['+', '/', '='], 'x', base64_encode(random_bytes(9))), 0, 10) . 'a1';
db_query('UPDATE users SET password_hash = ?, must_change = 1, failed_attempts = 0, locked_until = NULL WHERE id = ?',
    [password_hash($temp, PASSWORD_DEFAULT), $id]);
log_activity('password_reset_by_admin', $u['email']);
json_ok(['temp' => $temp, 'message' => 'Temporary password created.']);
