<?php
require __DIR__ . '/../../core/bootstrap.php';
guard_ajax('admin', ['admin']);
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_fail('Invalid request.', 405);

$id = (int)(request_input()['id'] ?? 0);
if ($id === (int)$_SESSION['user_id']) json_fail('You cannot disable your own account.');
$u = db_one("SELECT id, email, status FROM users WHERE id = ? AND status IN ('active','disabled')", [$id]);
if (!$u) json_fail('User not found.', 404);

$new = $u['status'] === 'active' ? 'disabled' : 'active';
db_query('UPDATE users SET status = ? WHERE id = ?', [$new, $id]);
log_activity('user_' . $new, $u['email']);
json_ok(['status' => $new, 'message' => 'Account ' . $new . '.']);
