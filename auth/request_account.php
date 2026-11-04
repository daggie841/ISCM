<?php
require __DIR__ . '/../core/bootstrap.php';
init_section('auth');
start_session();
auth_header('Request an account');
?>
<p class="note">An administrator will review your request and assign your role.</p>
<form class="ajax-form" data-endpoint="auth/ajax/request_account.php" data-reset="1">
  <label for="name">Full name</label>
  <input id="name" name="name" required maxlength="100">
  <label for="email">Email</label>
  <input id="email" name="email" type="email" required maxlength="150">
  <label for="password">Password</label>
  <input id="password" name="password" type="password" required minlength="8" autocomplete="new-password">
  <p class="note">At least 8 characters with a letter and a number.</p>
  <button class="btn" type="submit">Send request</button>
</form>
<div class="links"><a href="login.php">Back to log in</a></div>
<?php auth_footer(['auth/js/auth.js']);
