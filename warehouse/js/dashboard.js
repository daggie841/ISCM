const levelBadge = s => ({
  ok: ['good', 'In stock'],
  low: ['warn', 'Low'],
  out: ['bad', 'Out'],
  overstock: ['warn', 'Overstock']
}[s] || ['', s]);

(async () => {
  const r = await get_json(BASE + '/warehouse/ajax/dashboard_stats.php');
  if (!r.ok) {
    document.getElementById('lowest').innerHTML =
      '<tr><td colspan="6" class="empty">Could not load stock levels.</td></tr>';
    return show_message(r.message, 'error');
  }
  const s = r.data.stats;
  document.getElementById('k-low').textContent = s.low;
  document.getElementById('k-out').textContent = s.out;
  document.getElementById('k-over').textContent = s.overstock;
  document.getElementById('k-received').textContent = s.received_today;
  document.getElementById('k-issued').textContent = s.issued_today;

  const rows = r.data.lowest.map(p => {
    const [cls, label] = levelBadge(p.stock_status);
    return `<tr>
      <td>${esc(p.sku)}</td><td>${esc(p.name)}</td><td>${esc(p.category || '-')}</td>
      <td>${esc(p.total_qty)}</td><td>${esc(p.reorder_level)}</td>
      <td><span class="badge ${cls}">${label}</span></td>
    </tr>`;
  }).join('');
  document.getElementById('lowest').innerHTML =
    rows || '<tr><td colspan="6" class="empty">No active products yet.</td></tr>';
})();
