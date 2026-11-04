// One handler for every auth form: posts to data-endpoint and follows the reply.
document.querySelectorAll('form.ajax-form').forEach(form => {
  form.addEventListener('submit', async ev => {
    ev.preventDefault();
    const btn = form.querySelector('button[type=submit]');
    btn.disabled = true;
    const r = await post_json(BASE + '/' + form.dataset.endpoint, form_data(form));
    btn.disabled = false;
    if (!r.ok) return show_message(r.message || 'Something went wrong.', 'error');
    if (r.data?.message) show_message(r.data.message, 'ok');
    if (form.dataset.reset) form.reset();
    const go = r.data?.redirect || (form.dataset.redirect ? BASE + '/' + form.dataset.redirect : '');
    if (go) setTimeout(() => { location.href = go; }, r.data?.message ? 900 : 0);
  });
});
