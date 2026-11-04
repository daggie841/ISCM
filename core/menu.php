<?php
// Sidebar links per role. Each department adds its own links here.
function menu_for($role) {
    $m = [
        'admin' => [
            ['Dashboard', 'admin/pages/dashboard.php'],
            ['Users', 'admin/pages/users.php'],
            ['Products', 'admin/pages/products.php'],
            ['Settings', 'admin/pages/settings.php'],
            ['Logs', 'admin/pages/logs.php'],
        ],
        'warehouse' => [['Dashboard', 'warehouse/pages/dashboard.php']],
        'procurement' => [['Dashboard', 'procurement/pages/dashboard.php']],
        'sales' => [['Dashboard', 'sales/pages/dashboard.php']],
        'manager' => [['Dashboard', 'manager/pages/dashboard.php']],
    ];
    return $m[$role] ?? [];
}
