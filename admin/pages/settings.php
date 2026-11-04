<?php
require __DIR__ . '/../../core/bootstrap.php';
guard('admin', ['admin']);
page_header('Settings', 'Settings');
?>
<div class="tabs" id="tabs">
  <button class="on" data-tab="general">General</button>
  <button data-tab="alerts">Alerts</button>
  <button data-tab="forecast">Forecasting</button>
  <button data-tab="locations">Locations</button>
  <button data-tab="categories">Categories</button>
  <button data-tab="backup">Backup</button>
</div>

<form id="settings-form">
  <div class="pane on card" id="pane-general" style="max-width:520px">
    <label for="company_name">Company name</label><input id="company_name" name="company_name" maxlength="100">
    <label for="currency">Currency code</label><input id="currency" name="currency" maxlength="5">
    <label for="session_timeout_min">Log out inactive users after (minutes)</label><input id="session_timeout_min" name="session_timeout_min" type="number" min="5" max="480">
  </div>

  <div class="pane card" id="pane-alerts" style="max-width:640px">
    <h2>Stock and supplier thresholds</h2>
    <label for="low_stock_threshold">Low-stock threshold (units, used when a product has no reorder level)</label><input id="low_stock_threshold" name="low_stock_threshold" type="number" min="0">
    <label for="overstock_multiplier">Overstock when stock is above reorder level times</label><input id="overstock_multiplier" name="overstock_multiplier" type="number" min="1">
    <label for="supplier_delay_days">Supplier delay alert after (days late)</label><input id="supplier_delay_days" name="supplier_delay_days" type="number" min="0">
    <h2 style="margin-top:22px">Email</h2>
    <label for="email_enabled">Send alert emails</label>
    <select id="email_enabled" name="email_enabled"><option value="0">Off</option><option value="1">On</option></select>
    <label for="smtp_host">SMTP host</label><input id="smtp_host" name="smtp_host">
    <label for="smtp_port">SMTP port</label><input id="smtp_port" name="smtp_port" type="number">
    <label for="smtp_user">SMTP username</label><input id="smtp_user" name="smtp_user" autocomplete="off">
    <label for="smtp_pass">SMTP password</label><input id="smtp_pass" name="smtp_pass" type="password" autocomplete="new-password">
    <label for="mail_from">Sender email</label><input id="mail_from" name="mail_from" type="email">
    <h2 style="margin-top:22px">SMS</h2>
    <label for="sms_enabled">Send alert SMS</label>
    <select id="sms_enabled" name="sms_enabled"><option value="0">Off</option><option value="1">On</option></select>
    <label for="sms_username">SMS gateway username</label><input id="sms_username" name="sms_username" autocomplete="off">
    <label for="sms_api_key">SMS gateway API key</label><input id="sms_api_key" name="sms_api_key" type="password" autocomplete="new-password">
  </div>

  <div class="pane card" id="pane-forecast" style="max-width:520px">
    <label for="forecast_schedule">Nightly retraining time (24-hour)</label><input id="forecast_schedule" name="forecast_schedule" placeholder="02:00">
    <label for="forecast_horizons">Forecast horizons in days (comma separated: 7, 30, 90)</label><input id="forecast_horizons" name="forecast_horizons" placeholder="7,30,90">
    <p class="note">The schedule is read by the nightly job set up in the ML step.</p>
  </div>
  <div id="save-row" class="actions"><button class="btn" type="submit">Save settings</button></div>
</form>

<div class="pane card" id="pane-locations">
  <h2>Locations</h2>
  <div class="tablewrap"><table><thead><tr><th>Name</th><th>Address</th><th>Status</th><th></th></tr></thead><tbody id="loc-body"></tbody></table></div>
  <form id="loc-form" class="row" style="margin-top:14px">
    <input type="hidden" name="id" value="0">
    <div><label for="loc-name">Name</label><input id="loc-name" name="name" required maxlength="80"></div>
    <div><label for="loc-addr">Address</label><input id="loc-addr" name="address" maxlength="200"></div>
    <div><label for="loc-status">Status</label><select id="loc-status" name="status"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
    <div style="flex:0;white-space:nowrap"><button class="btn" type="submit">Save location</button></div>
  </form>
</div>

<div class="pane card" id="pane-categories">
  <h2>Categories</h2>
  <div class="tablewrap"><table><thead><tr><th>Name</th><th>Products</th><th></th></tr></thead><tbody id="cat-body"></tbody></table></div>
  <form id="cat-form" class="row" style="margin-top:14px">
    <input type="hidden" name="id" value="0">
    <div><label for="cat-name">Name</label><input id="cat-name" name="name" required maxlength="80"></div>
    <div style="flex:0;white-space:nowrap"><button class="btn" type="submit">Save category</button></div>
  </form>
</div>

<div class="pane card" id="pane-backup" style="max-width:520px">
  <h2>Database backup</h2>
  <p class="note">Creates a .sql file of every table in the protected backups folder.</p>
  <button class="btn" id="backup-btn" type="button">Create backup now</button>
  <p id="backup-result"></p>
</div>
<?php page_footer(['admin/js/settings.js']);
