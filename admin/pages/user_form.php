<?php
require __DIR__ . '/../../core/bootstrap.php';
guard('admin', ['admin']);
$id = (int)($_GET['id'] ?? 0);
$u = $id ? db_one("SELECT id, name, email, role_id, status FROM users WHERE id = ? AND status <> 'pending'", [$id]) : null;
if ($id && !$u) { http_response_code(404); echo 'User not found.'; exit; }
$roles = db_all('SELECT id, label FROM roles ORDER BY id');
$self = $u && (int)$u['id'] === (int)$_SESSION['user_id'];
page_header($u ? 'Edit user' : 'Add user', 'Users');
?>
<div class="card" style="max-width:520px">
  <form id="user-form">
    <input type="hidden" name="id" value="<?= (int)($u['id'] ?? 0) ?>">
    <label for="name">Full name</label>
    <input id="name" name="name" required maxlength="100" value="<?= e($u['name'] ?? '') ?>">
    <label for="email">Email</label>
    <input id="email" name="email" type="email" required maxlength="150" value="<?= e($u['email'] ?? '') ?>">
    <label for="role_id">Role</label>
    <select id="role_id" name="role_id" required <?= $self ? 'disabled' : '' ?>>
      <?php foreach ($roles as $r): ?>
        <option value="<?= (int)$r['id'] ?>" <?= ($u && (int)$u['role_id'] === (int)$r['id']) ? 'selected' : '' ?>><?= e($r['label']) ?></option>
      <?php endforeach; ?>
    </select>
    <label for="status">Status</label>
    <select id="status" name="status" <?= $self ? 'disabled' : '' ?>>
      <option value="active" <?= ($u['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active</option>
      <option value="disabled" <?= ($u['status'] ?? '') === 'disabled' ? 'selected' : '' ?>>Disabled</option>
    </select>
    <?php if (!$u): ?>
      <label for="password">Starting password</label>
      <input id="password" name="password" type="password" required minlength="8" autocomplete="new-password">
      <p class="note">The user must change it at first login.</p>
    <?php elseif ($self): ?>
      <p class="note">You cannot change your own role or status.</p>
    <?php endif; ?>
    <div class="actions" style="margin-top:18px">
      <button class="btn" type="submit">Save user</button>
      <a class="btn ghost" href="users.php">Cancel</a>
    </div>
  </form>
</div>
<?php page_footer(['admin/js/user_form.js']);
