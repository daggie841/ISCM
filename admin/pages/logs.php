<?php
require __DIR__ . '/../../core/bootstrap.php';
guard('admin', ['admin']);
page_header('Logs', 'Logs');
?>
<div class="tabs" id="tabs">
  <button class="on" data-tab="errors">Error logs</button>
  <button data-tab="activity">Activity log</button>
</div>

<div class="pane on" id="pane-errors">
  <div class="card">
    <div class="row">
      <div><label for="log-file">Log file</label><select id="log-file"></select></div>
      <div><label for="log-q">Search in log</label><input id="log-q" placeholder="Type to filter lines"></div>
      <div class="actions" style="flex:0;white-space:nowrap">
        <button class="btn ghost" id="log-refresh" type="button">Refresh</button>
        <a class="btn ghost" id="log-download" href="#">Download</a>
        <button class="btn danger" id="log-archive" type="button">Archive and clear</button>
      </div>
    </div>
    <p class="note" id="log-info" style="margin-top:12px"></p>
    <div class="logbox" id="log-box">Choose a log file.</div>
  </div>
</div>

<div class="pane" id="pane-activity">
  <div class="card">
    <div class="row">
      <div><label for="a-user">User</label><select id="a-user"><option value="0">All users</option></select></div>
      <div><label for="a-action">Action contains</label><input id="a-action" placeholder="e.g. login"></div>
      <div><label for="a-from">From</label><input id="a-from" type="date"></div>
      <div><label for="a-to">To</label><input id="a-to" type="date"></div>
    </div>
    <div class="tablewrap" style="margin-top:14px"><table>
      <thead><tr><th>Time</th><th>User</th><th>Action</th><th>Details</th><th>IP</th></tr></thead>
      <tbody id="a-body"></tbody>
    </table></div>
    <div class="pager"><button class="btn small ghost" id="a-prev">Previous</button><span id="a-info"></span><button class="btn small ghost" id="a-next">Next</button></div>
  </div>
</div>
<?php page_footer(['admin/js/logs.js']);
