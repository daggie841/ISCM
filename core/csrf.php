<?php
function csrf_token() {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function csrf_check() {
    $t = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($_POST['csrf'] ?? '');
    if (empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $t)) {
        log_error('auth', 'CSRF token rejected', ['uri' => $_SERVER['REQUEST_URI'] ?? '']);
        json_fail('Your session token expired. Refresh the page and try again.', 419);
    }
}
