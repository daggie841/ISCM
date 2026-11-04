<?php
require __DIR__ . '/../core/bootstrap.php';
init_section('auth');
require_login();
auth_header('Change your password');
if (!empty($_SESSION['must_change'])) echo '<p class="note">For security, set a new password before continuing.</p>';
?>
<form class="ajax-form" data-endpoint="auth/ajax/change_password.php" data-reset="1">
  <label for="current">Current password</label>
  <input id="current" name="current" type="password" required autocomplete="current-password">
  <label for="password">New password</label>
  <input id="password" name="password" type="password" required minlength="8" autocomplete="new-password">
  <label for="confirm">Confirm new password</label>
  <input id="confirm" name="confirm" type="password" required minlength="8" autocomplete="new-password">
  <button class="btn" type="submit">Change password</button>
</form>
<?php auth_footer(['auth/js/auth.js']);
