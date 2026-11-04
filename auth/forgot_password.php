<?php
require __DIR__ . '/../core/bootstrap.php';
init_section('auth');
start_session();
auth_header('Reset your password');
?>
<p class="note">Enter your email and we will create a reset link valid for 1 hour.</p>
<form class="ajax-form" data-endpoint="auth/ajax/reset_request.php" data-reset="1">
  <label for="email">Email</label>
  <input id="email" name="email" type="email" required>
  <button class="btn" type="submit">Send reset link</button>
</form>
<div class="links"><a href="login.php">Back to log in</a></div>
<?php auth_footer(['auth/js/auth.js']);
