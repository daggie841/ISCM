<?php
require __DIR__ . '/../../core/bootstrap.php';
guard('admin', ['admin']);
page_header('Products', 'Products');
?>
<div class="card">
  <div class="row">
    <div><label for="f-q">Search</label><input id="f-q" placeholder="Name, SKU or barcode"></div>
    <div><label for="f-cat">Category</label><select id="f-cat"><option value="">All categories</option></select></div>
    <div><label for="f-stock">Stock level</label>
      <select id="f-stock"><option value="">All</option><option value="ok">In stock</option><option value="low">Low</option><option value="out">Out of stock</option><option value="overstock">Overstock</option></select></div>
    <div><label for="f-status">Status</label>
      <select id="f-status"><option value="">All</option><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
    <div style="flex:0;white-space:nowrap"><a class="btn" href="product_form.php">Add product</a></div>
  </div>
  <div class="tablewrap" style="margin-top:14px"><table>
    <thead><tr><th>SKU</th><th>Product</th><th>Category</th><th>Cost</th><th>Price</th><th>In stock</th><th>Level</th><th>Status</th><th>Actions</th></tr></thead>
    <tbody id="products"><tr><td colspan="9" class="empty">Loading...</td></tr></tbody>
  </table></div>
  <div class="pager"><button class="btn small ghost" id="prev">Previous</button><span id="pageinfo"></span><button class="btn small ghost" id="next">Next</button></div>
</div>
<?php page_footer(['admin/js/products.js']);
