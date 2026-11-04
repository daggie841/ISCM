<?php
function log_activity($action, $details = '', $user_id = null) {
    try {
        db_query('INSERT INTO activity_log (user_id, action, details, ip) VALUES (?, ?, ?, ?)',
            [$user_id ?? ($_SESSION['user_id'] ?? null), $action, str_cut($details, 500), $_SERVER['REMOTE_ADDR'] ?? '']);
    } catch (Throwable $ex) { /* already logged by db_query */ }
}
