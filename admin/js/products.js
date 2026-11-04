let page = 1, catsLoaded = false, timer;
const $ = id => document.getElementById(id);
const money = n => Number(n).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
const levelBadge = s => ({ok: ['good', 'In stock'], low: ['warn', 'Low'], out: ['bad', 'Out'], overstock: ['warn', 'Overstock']}[s] || ['', s]);

async function load() {
  const r = await get_json(BASE + '/admin/ajax/product_list.php',
    {q: $('f-q').value, category: $('f-cat').value, stock: $('f-stock').value, status: $('f-status').value, page});
  if (!r.ok) return show_message(r.message, 'error');
  const d = r.data;
  if (!catsLoaded) {
    catsLoaded = true;
    $('f-cat').innerHTML += d.categories.map(c => `<option value="${c.id}">${esc(c.name)}</option>`).join('');
  }
  $('products').innerHTML = d.products.map(p => {
    const [cls, label] = levelBadge(p.stock_status);
    return `<tr>
      <td>${esc(p.sku)}</td><td>${esc(p.name)}<br><span class="note">${esc(p.supplier || 'No supplier')}</span></td>
      <td>${esc(p.category || '-')}</td><td>${money(p.unit_cost)}</td><td>${money(p.unit_price)}</td>
      <td>${esc(p.total_qty)}</td><td><span class="badge ${cls}">${label}</span></td>
      <td>${badge(p.status === 'active' ? 'active' : 'disabled')}</td>
      <td class="actions">
        <a class="btn small ghost" href="product_form.php?id=${p.id}">Edit</a>
        <button class="btn small ghost" data-act="toggle" data-id="${p.id}">${p.status === 'active' ? 'Deactivate' : 'Activate'}</button>
        <button class="btn small danger" data-act="delete" data-id="${p.id}" data-name="${esc(p.name)}">Delete</button>
      </td></tr>`;
  }).join('') || '<tr><td colspan="9" class="empty">No products match these filters.</td></tr>';
  const pages = Math.max(1, Math.ceil(d.total / d.per));
  $('pageinfo').textContent = `Page ${d.page} of ${pages} (${d.total} products)`;
  $('prev').disabled = d.page <= 1;
  $('next').disabled = d.page >= pages;
}

document.addEventListener('click', async ev => {
  const b = ev.target.closest('button[data-act]');
  if (!b) return;
  const id = +b.dataset.id;
  if (b.dataset.act === 'delete' && !confirm('Delete "' + b.dataset.name + '" and its stock records? This cannot be undone.')) return;
  b.disabled = true;
  const r = await post_json(BASE + '/admin/ajax/product_' + b.dataset.act + '.php', {id});
  b.disabled = false;
  show_message(r.ok ? r.data.message : r.message, r.ok ? 'ok' : 'error');
  if (r.ok) load();
});

['f-cat', 'f-stock', 'f-status'].forEach(i => $(i).addEventListener('change', () => { page = 1; load(); }));
$('f-q').addEventListener('input', () => { clearTimeout(timer); timer = setTimeout(() => { page = 1; load(); }, 300); });
$('prev').addEventListener('click', () => { page--; load(); });
$('next').addEventListener('click', () => { page++; load(); });
load();
