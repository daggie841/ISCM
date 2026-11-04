<?php
require __DIR__ . '/../../core/bootstrap.php';
guard('admin', ['admin']);
page_header('Dashboard', 'Dashboard');
?>
<div class="grid" id="kpis">
  <div class="kpi"><b id="k-users">-</b><span>Active users</span></div>
  <div class="kpi"><b id="k-pending">-</b><span>Account requests waiting</span></div>
  <div class="kpi"><b id="k-products">-</b><span>Active products</span></div>
  <div class="kpi"><b id="k-alerts">-</b><span>Open alerts</span></div>
  <div class="kpi"><b id="k-ml" style="font-size:1.1rem">-</b><span>Last forecast run</span></div>
</div>
<div class="card">
  <h2>Recent activity</h2>
  <div class="tablewrap"><table>
    <thead><tr><th>Time</th><th>User</th><th>Action</th><th>Details</th></tr></thead>
    <tbody id="recent"><tr><td colspan="4" class="empty">Loading...</td></tr></tbody>
  </table></div>
</div>
<?php page_footer(['admin/js/dashboard.js']);
