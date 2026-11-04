<?php
define('ROOT', dirname(__DIR__));
require_once ROOT . '/core/logger.php';
require_once ROOT . '/core/db.php';
require_once ROOT . '/core/response.php';
require_once ROOT . '/core/csrf.php';
require_once ROOT . '/core/validate.php';
require_once ROOT . '/core/settings.php';
require_once ROOT . '/core/activity.php';
require_once ROOT . '/core/auth.php';
require_once ROOT . '/core/layout.php';

function app_config($key = null) {
    static $c;
    if (!$c) $c = require ROOT . '/config/app.php';
    return $key === null ? $c : ($c[$key] ?? null);
}
function url($path = '') { return rtrim(app_config('base_url'), '/') . '/' . ltrim($path, '/'); }
function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
