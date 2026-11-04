<?php
require __DIR__ . '/../core/bootstrap.php';
init_section('auth');
start_session();
if (current_user()) { header('Location: ' . dashboard_url($_SESSION['role'])); exit; }
$timed_out = !empty($_SESSION['timed_out']);
unset($_SESSION['timed_out']);
auth_header('Log in');
if ($timed_out) echo '<p class="note">You were logged out because of inactivity.</p>';
?>
<form class="ajax-form" data-endpoint="auth/ajax/login.php">
  <label for="email">Email</label>
  <input id="email" name="email" type="email" required autocomplete="username">
  <label for="password">Password</label>
  <input id="password" name="password" type="password" required autocomplete="current-password">
  <button class="btn" type="submit">Log in</button>
</form>
<div class="links"><a href="forgot_password.php">Forgot password?</a><a href="request_account.php">Request an account</a></div>
<?php auth_footer(['auth/js/auth.js']);
