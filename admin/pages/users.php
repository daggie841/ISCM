<?php
require __DIR__ . '/../../core/bootstrap.php';
guard('admin', ['admin']);
page_header('Users', 'Users');
?>
<div class="card" id="pending-card" style="display:none">
  <h2>Account requests</h2>
  <div class="tablewrap"><table>
    <thead><tr><th>Name</th><th>Email</th><th>Requested</th><th>Assign role</th><th></th></tr></thead>
    <tbody id="pending"></tbody>
  </table></div>
</div>
<div class="card">
  <div class="row">
    <div><label for="f-q">Search</label><input id="f-q" placeholder="Name or email"></div>
    <div><label for="f-role">Role</label><select id="f-role"><option value="">All roles</option></select></div>
    <div><label for="f-status">Status</label>
      <select id="f-status"><option value="">All</option><option value="active">Active</option><option value="disabled">Disabled</option></select></div>
    <div style="flex:0"><a class="btn" href="user_form.php">Add user</a></div>
  </div>
  <div class="tablewrap" style="margin-top:14px"><table>
    <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Last login</th><th>Actions</th></tr></thead>
    <tbody id="users"><tr><td colspan="6" class="empty">Loading...</td></tr></tbody>
  </table></div>
  <div class="pager"><button class="btn small ghost" id="prev">Previous</button><span id="pageinfo"></span><button class="btn small ghost" id="next">Next</button></div>
</div>
<?php page_footer(['admin/js/users.js']);
