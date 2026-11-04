<?php
require __DIR__ . '/../../core/bootstrap.php';
guard_ajax('admin', ['admin']);
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_fail('Invalid request.', 405);

$in = request_input();
$id = (int)($in['id'] ?? 0);
$role_id = (int)($in['role_id'] ?? 0);
if (!db_one('SELECT id FROM roles WHERE id = ?', [$role_id])) json_fail('Choose a role for this user.');
$u = db_one("SELECT email FROM users WHERE id = ? AND status = 'pending'", [$id]);
if (!$u) json_fail('Request not found.', 404);

db_query("UPDATE users SET status = 'active', role_id = ? WHERE id = ?", [$role_id, $id]);
log_activity('request_approved', $u['email'] . " (role $role_id)");
json_ok(['message' => 'Account approved.']);
