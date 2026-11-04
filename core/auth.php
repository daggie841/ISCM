<?php
function is_ajax() {
    return strpos($_SERVER['SCRIPT_NAME'] ?? '', '/ajax/') !== false || ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'fetch';
}
function start_session() {
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax', 'path' => '/']);
        session_start();
    }
    if (!empty($_SESSION['user_id'])) {
        $limit = (int)setting('session_timeout_min', 30) * 60;
        if (time() - ($_SESSION['last'] ?? time()) > $limit) {
            session_unset();
            session_destroy();
            session_start();
            $_SESSION['timed_out'] = 1;
        } else {
            $_SESSION['last'] = time();
        }
    }
}
function current_user() {
    if (empty($_SESSION['user_id'])) return null;
    return ['id' => (int)$_SESSION['user_id'], 'name' => $_SESSION['name'], 'role' => $_SESSION['role']];
}
function dashboard_url($role) { return url($role . '/pages/dashboard.php'); }
function require_login() {
    start_session();
    if (!current_user()) {
        if (is_ajax()) json_fail('Session expired. Please log in again.', 401);
        header('Location: ' . url('auth/login.php'));
        exit;
    }
}
function require_role(array $roles) {
    require_login();
    if (!empty($_SESSION['must_change'])) {
        if (is_ajax()) json_fail('You must change your password first.', 403);
        header('Location: ' . url('auth/change_password.php'));
        exit;
    }
    if (!in_array($_SESSION['role'], $roles, true)) {
        log_error('auth', 'Forbidden access', ['role' => $_SESSION['role'], 'uri' => $_SERVER['REQUEST_URI'] ?? '']);
        if (is_ajax()) json_fail('You do not have permission for this action.', 403);
        http_response_code(403);
        echo 'You do not have permission to view this page.';
        exit;
    }
}
// One call at the top of every page: section log, session, role check.
function guard($section, array $roles) {
    init_section($section);
    require_role($roles);
}
// Same for ajax files; also checks the CSRF token on POST.
function guard_ajax($section, array $roles) {
    init_section($section);
    start_session();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') csrf_check();
    require_role($roles);
}
