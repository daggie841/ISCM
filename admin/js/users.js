let page = 1, roles = [], timer;
const $ = id => document.getElementById(id);

async function load() {
  const r = await get_json(BASE + '/admin/ajax/user_list.php', {q: $('f-q').value, role: $('f-role').value, status: $('f-status').value, page});
  if (!r.ok) return show_message(r.message, 'error');
  const d = r.data;
  if (!roles.length) {
    roles = d.roles;
    $('f-role').innerHTML += roles.map(x => `<option value="${esc(x.name)}">${esc(x.label)}</option>`).join('');
  }
  $('users').innerHTML = d.users.map(u => `<tr>
    <td>${esc(u.name)}</td><td>${esc(u.email)}</td><td>${esc(u.role_label || '-')}</td><td>${badge(u.status)}</td>
    <td>${esc(u.last_login || 'Never')}</td>
    <td class="actions">
      <a class="btn small ghost" href="user_form.php?id=${u.id}">Edit</a>
      <button class="btn small ghost" data-act="reset" data-id="${u.id}">Reset password</button>
      ${u.id === d.me ? '' : `<button class="btn small ${u.status === 'active' ? 'danger' : 'ghost'}" data-act="toggle" data-id="${u.id}">${u.status === 'active' ? 'Disable' : 'Enable'}</button>`}
    </td></tr>`).join('') || '<tr><td colspan="6" class="empty">No users match these filters.</td></tr>';
  const pages = Math.max(1, Math.ceil(d.total / d.per));
  $('pageinfo').textContent = `Page ${d.page} of ${pages} (${d.total} users)`;
  $('prev').disabled = d.page <= 1;
  $('next').disabled = d.page >= pages;

  $('pending-card').style.display = d.pending.length ? 'block' : 'none';
  const opts = roles.map(x => `<option value="${x.id}">${esc(x.label)}</option>`).join('');
  $('pending').innerHTML = d.pending.map(p => `<tr>
    <td>${esc(p.name)}</td><td>${esc(p.email)}</td><td>${esc(p.created_at)}</td>
    <td><select id="role-${p.id}">${opts}</select></td>
    <td class="actions"><button class="btn small" data-act="approve" data-id="${p.id}">Approve</button>
    <button class="btn small danger" data-act="reject" data-id="${p.id}">Reject</button></td></tr>`).join('');
}

document.addEventListener('click', async ev => {
  const b = ev.target.closest('button[data-act]');
  if (!b) return;
  const id = +b.dataset.id, act = b.dataset.act;
  let url, body = {id};
  if (act === 'toggle') url = 'user_toggle_status.php';
  else if (act === 'reset') {
    if (!confirm('Create a new temporary password for this user?')) return;
    url = 'user_reset_password.php';
  } else if (act === 'approve') { url = 'request_approve.php'; body.role_id = +$('role-' + id).value; }
  else if (act === 'reject') {
    if (!confirm('Reject and delete this request?')) return;
    url = 'request_reject.php';
  }
  b.disabled = true;
  const r = await post_json(BASE + '/admin/ajax/' + url, body);
  b.disabled = false;
  if (!r.ok) return show_message(r.message, 'error');
  if (r.data.temp) prompt('Temporary password (copy it now, it is shown once):', r.data.temp);
  show_message(r.data.message, 'ok');
  load();
});

['f-role', 'f-status'].forEach(i => $(i).addEventListener('change', () => { page = 1; load(); }));
$('f-q').addEventListener('input', () => { clearTimeout(timer); timer = setTimeout(() => { page = 1; load(); }, 300); });
$('prev').addEventListener('click', () => { page--; load(); });
$('next').addEventListener('click', () => { page++; load(); });
load();
