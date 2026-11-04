<?php
require __DIR__ . '/../core/bootstrap.php';
init_section('auth');
start_session();
$token = preg_replace('/[^a-f0-9]/', '', $_GET['token'] ?? '');
auth_header('Choose a new password');
?>
<form class="ajax-form" data-endpoint="auth/ajax/reset_confirm.php" data-redirect="auth/login.php">
  <input type="hidden" name="token" value="<?= e($token) ?>">
  <label for="password">New password</label>
  <input id="password" name="password" type="password" required minlength="8" autocomplete="new-password">
  <label for="confirm">Confirm password</label>
  <input id="confirm" name="confirm" type="password" required minlength="8" autocomplete="new-password">
  <button class="btn" type="submit">Save password</button>
</form>
<?php auth_footer(['auth/js/auth.js']);
