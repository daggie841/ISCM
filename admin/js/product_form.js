const form = document.getElementById('product-form');
// A USB barcode scanner types the code and presses Enter, so Enter must not submit the form here.
form.elements.barcode.addEventListener('keydown', ev => { if (ev.key === 'Enter') { ev.preventDefault(); form.elements.name.focus(); } });

form.addEventListener('submit', async ev => {
  ev.preventDefault();
  const btn = form.querySelector('button[type=submit]');
  const data = form_data(form);
  data.stock = {};
  form.querySelectorAll('input[data-loc]').forEach(i => { data.stock[i.dataset.loc] = i.value; });
  btn.disabled = true;
  const r = await post_json(BASE + '/admin/ajax/product_save.php', data);
  btn.disabled = false;
  if (!r.ok) return show_message(r.message, 'error');
  show_message(r.data.message, 'ok');
  setTimeout(() => { location.href = 'products.php'; }, 800);
});
