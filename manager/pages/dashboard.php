<?php
require __DIR__ . '/../../core/bootstrap.php';
guard('manager', ['manager', 'admin']);
page_header('Dashboard', 'Dashboard');
?>
<div class="card"><p class="empty">The manager department is built in a later step.</p></div>
<?php page_footer();
