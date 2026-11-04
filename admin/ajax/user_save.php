<?php
require __DIR__ . '/../../core/bootstrap.php';
guard_ajax('admin', ['admin']);
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_fail('Invalid request.', 405);

$in = request_input();
$id = (int)($in['id'] ?? 0);
$name = clean_str($in['name'] ?? '', 100);
$email = strtolower(clean_str($in['email'] ?? ''));
$role_id = (int)($in['role_id'] ?? 0);
$status = $in['status'] ?? 'active';
$pass = (string)($in['password'] ?? '');

if ($name === '' || !valid_email($email)) json_fail('Enter a name and a valid email.');
if (!db_one('SELECT id FROM roles WHERE id = ?', [$role_id])) json_fail('Choose a role.');
if (!in_array($status, ['active', 'disabled'], true)) json_fail('Invalid status.');
if (db_one('SELECT id FROM users WHERE email = ? AND id <> ?', [$email, $id])) json_fail('Another user already has that email.');

if ($id === 0) {
    if (!valid_password($pass)) json_fail('Password needs 8+ characters with a letter and a number.');
    $id = db_insert('INSERT INTO users (name, email, password_hash, role_id, status, must_change) VALUES (?, ?, ?, ?, ?, 1)',
        [$name, $email, password_hash($pass, PASSWORD_DEFAULT), $role_id, $status]);
    log_activity('user_created', "$email (role $role_id)");
    json_ok(['id' => $id, 'message' => 'User created. They must change the password at first login.']);
}

$old = db_one('SELECT id, role_id, status FROM users WHERE id = ?', [$id]);
if (!$old) json_fail('User not found.', 404);
if ($id === (int)$_SESSION['user_id'] && ((int)$old['role_id'] !== $role_id || $status !== 'active')) {
    json_fail('You cannot change your own role or disable yourself.');
}
db_query('UPDATE users SET name = ?, email = ?, role_id = ?, status = ? WHERE id = ?', [$name, $email, $role_id, $status, $id]);
log_activity('user_updated', "$email (role $role_id, $status)");
json_ok(['id' => $id, 'message' => 'User saved.']);
