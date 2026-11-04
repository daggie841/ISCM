<?php
require __DIR__ . '/../../core/bootstrap.php';
init_section('auth');
start_session();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_fail('Invalid request.', 405);
csrf_check();

$in = request_input();
$email = strtolower(clean_str($in['email'] ?? ''));
$pass = (string)($in['password'] ?? '');
if (!valid_email($email) || $pass === '') json_fail('Enter your email and password.');

$u = db_one('SELECT u.*, r.name AS role FROM users u LEFT JOIN roles r ON r.id = u.role_id WHERE u.email = ?', [$email]);
$bad = 'Wrong email or password.';
if (!$u) {
    log_activity('login_failed', "Unknown email $email");
    json_fail($bad, 401);
}
if ($u['locked_until'] && strtotime($u['locked_until']) > time()) {
    json_fail('Too many failed attempts. Try again later.', 429);
}
if (!password_verify($pass, $u['password_hash'])) {
    $n = (int)$u['failed_attempts'] + 1;
    if ($n >= (int)app_config('max_attempts')) {
        $until = date('Y-m-d H:i:s', time() + 60 * (int)app_config('lock_minutes'));
        db_query('UPDATE users SET failed_attempts = 0, locked_until = ? WHERE id = ?', [$until, $u['id']]);
        log_activity('account_locked', $email, $u['id']);
    } else {
        db_query('UPDATE users SET failed_attempts = ? WHERE id = ?', [$n, $u['id']]);
    }
    log_activity('login_failed', "Bad password for $email", $u['id']);
    json_fail($bad, 401);
}
if ($u['status'] === 'pending') json_fail('Your account is waiting for administrator approval.', 403);
if ($u['status'] === 'disabled' || !$u['role']) json_fail('This account is disabled. Contact the administrator.', 403);

db_query('UPDATE users SET failed_attempts = 0, locked_until = NULL, last_login = NOW() WHERE id = ?', [$u['id']]);
session_regenerate_id(true);
$_SESSION['user_id'] = (int)$u['id'];
$_SESSION['name'] = $u['name'];
$_SESSION['role'] = $u['role'];
$_SESSION['must_change'] = (int)$u['must_change'];
$_SESSION['last'] = time();
log_activity('login', $email, $u['id']);

json_ok(['redirect' => $u['must_change'] ? url('auth/change_password.php') : dashboard_url($u['role'])]);
