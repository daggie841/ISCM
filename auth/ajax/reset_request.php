<?php
require __DIR__ . '/../../core/bootstrap.php';
init_section('auth');
start_session();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_fail('Invalid request.', 405);
csrf_check();

$email = strtolower(clean_str(request_input()['email'] ?? ''));
$same = 'If that account exists, a reset link has been created.';
if (!valid_email($email)) json_fail('Enter a valid email.');

$u = db_one("SELECT id, name FROM users WHERE email = ? AND status = 'active'", [$email]);
if ($u) {
    $token = bin2hex(random_bytes(32));
    db_query('INSERT INTO password_resets (user_id, token_hash, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 1 HOUR))',
        [$u['id'], hash('sha256', $token)]);
    $link = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . ($_SERVER['HTTP_HOST'] ?? 'localhost') . url('auth/reset_password.php?token=' . $token);
    $sent = false;
    if (setting('email_enabled') === '1') {
        $sent = @mail($email, 'Password reset', "Hello {$u['name']},\n\nReset your password (valid 1 hour):\n$link\n", 'From: ' . setting('mail_from', 'noreply@example.com'));
    }
    // With email off the link is written to the log so the admin can pass it on.
    if (!$sent) log_info('auth', "Reset link for $email: $link");
    log_activity('reset_requested', $email, $u['id']);
}
json_ok(['message' => $same]);
