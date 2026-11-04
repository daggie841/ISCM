<?php
require __DIR__ . '/../../core/bootstrap.php';
guard('sales', ['sales', 'admin']);
page_header('Dashboard', 'Dashboard');
?>
<div class="card"><p class="empty">The sales department is built in a later step.</p></div>
<?php page_footer();
