(async () => {
  const r = await get_json(BASE + '/admin/ajax/dashboard_stats.php');
  if (!r.ok) return show_message(r.message, 'error');
  const s = r.data.stats;
  document.getElementById('k-users').textContent = s.users;
  document.getElementById('k-pending').textContent = s.pending;
  document.getElementById('k-products').textContent = s.products;
  document.getElementById('k-alerts').textContent = s.alerts;
  document.getElementById('k-ml').textContent = s.last_ml || 'Never run';
  const rows = r.data.recent.map(a =>
    `<tr><td>${esc(a.created_at)}</td><td>${esc(a.user)}</td><td>${esc(a.action)}</td><td>${esc(a.details)}</td></tr>`).join('');
  document.getElementById('recent').innerHTML = rows || '<tr><td colspan="4" class="empty">No activity yet.</td></tr>';
})();
