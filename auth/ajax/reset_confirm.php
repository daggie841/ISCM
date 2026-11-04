<?php
require __DIR__ . '/../../core/bootstrap.php';
init_section('auth');
start_session();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_fail('Invalid request.', 405);
csrf_check();

$in = request_input();
$token = preg_replace('/[^a-f0-9]/', '', $in['token'] ?? '');
$pass = (string)($in['password'] ?? '');
if ($pass !== (string)($in['confirm'] ?? '')) json_fail('The two passwords do not match.');
if (!valid_password($pass)) json_fail('Password needs 8+ characters with a letter and a number.');

$r = db_one('SELECT * FROM password_resets WHERE token_hash = ? AND used = 0 AND expires_at > NOW()', [hash('sha256', $token)]);
if (!$r) json_fail('This reset link is invalid or has expired. Request a new one.');

db_query('UPDATE users SET password_hash = ?, must_change = 0, failed_attempts = 0, locked_until = NULL WHERE id = ?',
    [password_hash($pass, PASSWORD_DEFAULT), $r['user_id']]);
db_query('UPDATE password_resets SET used = 1 WHERE user_id = ?', [$r['user_id']]);
log_activity('password_reset', '', $r['user_id']);
json_ok(['message' => 'Password updated. You can log in now.']);
