<?php
require __DIR__ . '/../../core/bootstrap.php';
guard_ajax('admin', ['admin']);
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_fail('Invalid request.', 405);

$id = (int)(request_input()['id'] ?? 0);
$u = db_one("SELECT email FROM users WHERE id = ? AND status = 'pending'", [$id]);
if (!$u) json_fail('Request not found.', 404);

db_query("DELETE FROM users WHERE id = ? AND status = 'pending'", [$id]);
log_activity('request_rejected', $u['email']);
json_ok(['message' => 'Request rejected.']);
