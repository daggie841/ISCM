<?php
function setting($key, $default = null) {
    static $cache;
    if ($cache === null) {
        $cache = [];
        try {
            foreach (db_all('SELECT setting_key, setting_value FROM settings') as $r) $cache[$r['setting_key']] = $r['setting_value'];
        } catch (Throwable $ex) { /* table may not exist yet */ }
    }
    return $cache[$key] ?? $default;
}
function setting_save($key, $value) {
    db_query('INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)', [$key, $value]);
}
