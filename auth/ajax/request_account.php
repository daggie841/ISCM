<?php
require __DIR__ . '/../../core/bootstrap.php';
init_section('auth');
start_session();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_fail('Invalid request.', 405);
csrf_check();

$in = request_input();
$name = clean_str($in['name'] ?? '', 100);
$email = strtolower(clean_str($in['email'] ?? ''));
$pass = (string)($in['password'] ?? '');
if ($name === '' || !valid_email($email)) json_fail('Enter your name and a valid email.');
if (!valid_password($pass)) json_fail('Password needs 8+ characters with a letter and a number.');
if (db_one('SELECT id FROM users WHERE email = ?', [$email])) json_fail('That email already has an account or a pending request.');

$id = db_insert("INSERT INTO users (name, email, password_hash, status) VALUES (?, ?, ?, 'pending')",
    [$name, $email, password_hash($pass, PASSWORD_DEFAULT)]);
log_activity('account_requested', $email, $id);
json_ok(['message' => 'Request sent. An administrator will approve your account.']);
