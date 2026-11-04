const CSRF = document.querySelector('meta[name=csrf]')?.content || '';
const BASE = document.querySelector('meta[name=base]')?.content || '';

function esc(s) {
  return String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
}
function show_message(msg, type = 'info') {
  const t = document.getElementById('toast');
  if (!t) return;
  t.textContent = msg;
  t.className = 'toast show ' + type;
  clearTimeout(t._h);
  t._h = setTimeout(() => t.classList.remove('show'), 4000);
}
async function request(url, opts) {
  try {
    const r = await fetch(url, opts);
    const j = await r.json().catch(() => ({ok: false, message: 'Unexpected server reply.'}));
    if (r.status === 401) { location.href = BASE + '/auth/login.php'; }
    return j;
  } catch (err) {
    return {ok: false, message: 'Cannot reach the server. Check your connection.'};
  }
}
function post_json(url, data) {
  return request(url, {
    method: 'POST',
    headers: {'Content-Type': 'application/json', 'X-CSRF-Token': CSRF, 'X-Requested-With': 'fetch'},
    body: JSON.stringify(data || {})
  });
}
function get_json(url, params) {
  const q = params ? '?' + new URLSearchParams(params).toString() : '';
  return request(url + q, {headers: {'X-Requested-With': 'fetch'}});
}
function form_data(form) {
  const o = {};
  new FormData(form).forEach((v, k) => { o[k] = v; });
  return o;
}
function badge(text) {
  return '<span class="badge ' + esc(text) + '">' + esc(text) + '</span>';
}
document.getElementById('logout-btn')?.addEventListener('click', async () => {
  const r = await post_json(BASE + '/auth/ajax/logout.php');
  location.href = r.data?.redirect || BASE + '/auth/login.php';
});
