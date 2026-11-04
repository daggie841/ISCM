const $ = id => document.getElementById(id);
const AJ = BASE + '/admin/ajax/';
let aPage = 1, usersLoaded = false, timer;

document.getElementById('tabs').addEventListener('click', ev => {
  const b = ev.target.closest('button[data-tab]');
  if (!b) return;
  document.querySelectorAll('#tabs button').forEach(x => x.classList.toggle('on', x === b));
  $('pane-errors').classList.toggle('on', b.dataset.tab === 'errors');
  $('pane-activity').classList.toggle('on', b.dataset.tab === 'activity');
  if (b.dataset.tab === 'activity') loadActivity();
});

async function loadFiles(keep) {
  const r = await get_json(AJ + 'log_list.php');
  if (!r.ok) return show_message(r.message, 'error');
  const files = r.data.files;
  $('log-file').innerHTML = files.map(f => `<option value="${esc(f.name)}">${esc(f.name)} (${Math.round(f.size / 1024 * 10) / 10} KB, ${esc(f.modified)})</option>`).join('')
    || '<option value="">No log files yet</option>';
  if (keep) $('log-file').value = keep;
  readLog();
}
async function readLog() {
  const f = $('log-file').value;
  if (!f) { $('log-box').textContent = 'No errors have been logged yet.'; $('log-info').textContent = ''; return; }
  const r = await get_json(AJ + 'log_read.php', {file: f, q: $('log-q').value});
  if (!r.ok) return show_message(r.message, 'error');
  $('log-box').textContent = r.data.lines.join('\n') || 'No lines match.';
  $('log-info').textContent = `Showing newest ${r.data.lines.length} of ${r.data.total} lines.`;
  $('log-download').href = AJ + 'log_download.php?file=' + encodeURIComponent(f);
}
$('log-file').addEventListener('change', readLog);
$('log-refresh').addEventListener('click', () => loadFiles($('log-file').value));
$('log-q').addEventListener('input', () => { clearTimeout(timer); timer = setTimeout(readLog, 300); });
$('log-archive').addEventListener('click', async () => {
  const f = $('log-file').value;
  if (!f || !confirm('Save a copy in logs/archive and empty ' + f + '?')) return;
  const r = await post_json(AJ + 'log_archive.php', {file: f});
  show_message(r.ok ? r.data.message : r.message, r.ok ? 'ok' : 'error');
  if (r.ok) loadFiles(f);
});

async function loadActivity() {
  const r = await get_json(AJ + 'activity_list.php', {user: $('a-user').value, action: $('a-action').value, from: $('a-from').value, to: $('a-to').value, page: aPage});
  if (!r.ok) return show_message(r.message, 'error');
  const d = r.data;
  if (!usersLoaded) {
    usersLoaded = true;
    $('a-user').innerHTML += d.users.map(u => `<option value="${u.id}">${esc(u.name)}</option>`).join('');
  }
  $('a-body').innerHTML = d.rows.map(a => `<tr><td>${esc(a.created_at)}</td><td>${esc(a.user)}</td><td>${esc(a.action)}</td><td>${esc(a.details)}</td><td>${esc(a.ip)}</td></tr>`).join('')
    || '<tr><td colspan="5" class="empty">No activity matches these filters.</td></tr>';
  const pages = Math.max(1, Math.ceil(d.total / d.per));
  $('a-info').textContent = `Page ${d.page} of ${pages} (${d.total} entries)`;
  $('a-prev').disabled = d.page <= 1;
  $('a-next').disabled = d.page >= pages;
}
['a-user', 'a-from', 'a-to'].forEach(i => $(i).addEventListener('change', () => { aPage = 1; loadActivity(); }));
$('a-action').addEventListener('input', () => { clearTimeout(timer); timer = setTimeout(() => { aPage = 1; loadActivity(); }, 300); });
$('a-prev').addEventListener('click', () => { aPage--; loadActivity(); });
$('a-next').addEventListener('click', () => { aPage++; loadActivity(); });
loadFiles();
