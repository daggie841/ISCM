const $ = id => document.getElementById(id);
const AJ = BASE + '/admin/ajax/';
let locs = [], cats = [];

document.getElementById('tabs').addEventListener('click', ev => {
  const b = ev.target.closest('button[data-tab]');
  if (!b) return;
  document.querySelectorAll('#tabs button').forEach(x => x.classList.toggle('on', x === b));
  const t = b.dataset.tab;
  document.querySelectorAll('.pane').forEach(p => p.classList.remove('on'));
  const formTabs = ['general', 'alerts', 'forecast'];
  $('settings-form').style.display = formTabs.includes(t) ? 'block' : 'none';
  $('pane-' + t).classList.add('on');
});

async function loadSettings() {
  const r = await get_json(AJ + 'settings_get.php');
  if (!r.ok) return show_message(r.message, 'error');
  Object.entries(r.data.settings).forEach(([k, v]) => { const el = $(k); if (el) el.value = v; });
}
$('settings-form').addEventListener('submit', async ev => {
  ev.preventDefault();
  const r = await post_json(AJ + 'settings_save.php', form_data(ev.target));
  show_message(r.ok ? r.data.message : r.message, r.ok ? 'ok' : 'error');
  if (r.ok) loadSettings();
});

async function loadLocs() {
  const r = await get_json(AJ + 'location_list.php');
  if (!r.ok) return show_message(r.message, 'error');
  locs = r.data.locations;
  $('loc-body').innerHTML = locs.map(l => `<tr><td>${esc(l.name)}</td><td>${esc(l.address || '')}</td><td>${badge(l.status === 'active' ? 'active' : 'disabled')}</td>
    <td><button class="btn small ghost" data-loc="${l.id}">Edit</button></td></tr>`).join('') || '<tr><td colspan="4" class="empty">Add your first location below.</td></tr>';
}
$('loc-body').addEventListener('click', ev => {
  const b = ev.target.closest('[data-loc]');
  if (!b) return;
  const l = locs.find(x => x.id == b.dataset.loc), f = $('loc-form');
  f.id.value = l.id; f.name.value = l.name; f.address.value = l.address || ''; f.status.value = l.status;
  f.name.focus();
});
$('loc-form').addEventListener('submit', async ev => {
  ev.preventDefault();
  const r = await post_json(AJ + 'location_save.php', form_data(ev.target));
  show_message(r.ok ? r.data.message : r.message, r.ok ? 'ok' : 'error');
  if (r.ok) { ev.target.reset(); ev.target.id.value = 0; loadLocs(); }
});

async function loadCats() {
  const r = await get_json(AJ + 'category_list.php');
  if (!r.ok) return show_message(r.message, 'error');
  cats = r.data.categories;
  $('cat-body').innerHTML = cats.map(c => `<tr><td>${esc(c.name)}</td><td>${esc(c.products)}</td>
    <td><button class="btn small ghost" data-cat="${c.id}">Rename</button></td></tr>`).join('') || '<tr><td colspan="3" class="empty">Add your first category below.</td></tr>';
}
$('cat-body').addEventListener('click', ev => {
  const b = ev.target.closest('[data-cat]');
  if (!b) return;
  const c = cats.find(x => x.id == b.dataset.cat), f = $('cat-form');
  f.id.value = c.id; f.name.value = c.name; f.name.focus();
});
$('cat-form').addEventListener('submit', async ev => {
  ev.preventDefault();
  const r = await post_json(AJ + 'category_save.php', form_data(ev.target));
  show_message(r.ok ? r.data.message : r.message, r.ok ? 'ok' : 'error');
  if (r.ok) { ev.target.reset(); ev.target.id.value = 0; loadCats(); }
});

$('backup-btn').addEventListener('click', async ev => {
  ev.target.disabled = true;
  const r = await post_json(AJ + 'backup.php');
  ev.target.disabled = false;
  if (!r.ok) return show_message(r.message, 'error');
  show_message(r.data.message, 'ok');
  $('backup-result').innerHTML = `<a href="${AJ}backup_download.php?file=${encodeURIComponent(r.data.file)}">Download ${esc(r.data.file)}</a>`;
});

loadSettings(); loadLocs(); loadCats();
