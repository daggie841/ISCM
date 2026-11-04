<?php
require __DIR__ . '/../../core/bootstrap.php';
guard('warehouse', ['warehouse', 'admin']);
page_header('Dashboard', 'Dashboard');
?>
<div class="grid">
  <div class="kpi"><b id="k-low">-</b><span>Low stock products</span></div>
  <div class="kpi"><b id="k-out">-</b><span>Out of stock</span></div>
  <div class="kpi"><b id="k-over">-</b><span>Overstocked products</span></div>
  <div class="kpi"><b id="k-received">-</b><span>Units received today</span></div>
  <div class="kpi"><b id="k-issued">-</b><span>Units issued today</span></div>
</div>
<div class="card">
  <h2>Lowest stock</h2>
  <p class="note">Active products with the least stock across all locations.</p>
  <div class="tablewrap"><table>
    <thead><tr><th>SKU</th><th>Product</th><th>Category</th><th>On hand</th><th>Reorder level</th><th>Level</th></tr></thead>
    <tbody id="lowest"><tr><td colspan="6" class="empty">Loading...</td></tr></tbody>
  </table></div>
</div>
<?php page_footer(['warehouse/js/dashboard.js']);
