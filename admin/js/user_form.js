document.getElementById('user-form').addEventListener('submit', async ev => {
  ev.preventDefault();
  const f = ev.target, btn = f.querySelector('button[type=submit]');
  const data = form_data(f);
  // Disabled fields (own account) are not in FormData, so send their current values.
  ['role_id', 'status'].forEach(n => { if (f.elements[n].disabled) data[n] = f.elements[n].value; });
  btn.disabled = true;
  const r = await post_json(BASE + '/admin/ajax/user_save.php', data);
  btn.disabled = false;
  if (!r.ok) return show_message(r.message, 'error');
  show_message(r.data.message, 'ok');
  setTimeout(() => { location.href = 'users.php'; }, 900);
});
