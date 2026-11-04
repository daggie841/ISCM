<?php
require __DIR__ . '/../../core/bootstrap.php';
guard_ajax('admin', ['admin']);

$stats = [
    'users'    => (int)db_val("SELECT COUNT(*) FROM users WHERE status = 'active'"),
    'pending'  => (int)db_val("SELECT COUNT(*) FROM users WHERE status = 'pending'"),
    'products' => (int)db_val("SELECT COUNT(*) FROM products WHERE status = 'active'"),
    'alerts'   => (int)db_val("SELECT COUNT(*) FROM alerts WHERE status = 'open'"),
    'last_ml'  => db_val('SELECT MAX(run_at) FROM forecasts'),
];
$recent = db_all('SELECT a.created_at, COALESCE(u.name, \'-\') AS user, a.action, a.details
                  FROM activity_log a LEFT JOIN users u ON u.id = a.user_id ORDER BY a.id DESC LIMIT 10');
json_ok(['stats' => $stats, 'recent' => $recent]);
