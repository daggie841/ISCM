<?php
require __DIR__ . '/../../core/bootstrap.php';
init_section('auth');
start_session();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_fail('Invalid request.', 405);
csrf_check();
require_login();

$in = request_input();
$uid = (int)$_SESSION['user_id'];
$u = db_one('SELECT password_hash FROM users WHERE id = ?', [$uid]);
if (!$u || !password_verify((string)($in['current'] ?? ''), $u['password_hash'])) json_fail('Current password is wrong.');
$pass = (string)($in['password'] ?? '');
if ($pass !== (string)($in['confirm'] ?? '')) json_fail('The two new passwords do not match.');
if (!valid_password($pass)) json_fail('Password needs 8+ characters with a letter and a number.');
if (password_verify($pass, $u['password_hash'])) json_fail('Choose a password different from the current one.');

db_query('UPDATE users SET password_hash = ?, must_change = 0 WHERE id = ?', [password_hash($pass, PASSWORD_DEFAULT), $uid]);
$_SESSION['must_change'] = 0;
log_activity('password_changed');
json_ok(['message' => 'Password changed.', 'redirect' => dashboard_url($_SESSION['role'])]);
