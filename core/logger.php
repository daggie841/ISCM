<?php
function log_write($level, $section, $msg, $ctx = []) {
    $dir = dirname(__DIR__) . '/logs';
    if (!is_dir($dir)) @mkdir($dir, 0775, true);
    $section = preg_replace('/[^a-z_]/', '', strtolower($section)) ?: 'general';
    $uid = $_SESSION['user_id'] ?? 0;
    $line = sprintf("[%s] %s uid=%s %s %s\n", date('Y-m-d H:i:s'), $level, $uid, $msg,
        $ctx ? json_encode($ctx, JSON_UNESCAPED_SLASHES) : '');
    @file_put_contents($dir . '/' . $section . '.log', $line, FILE_APPEND | LOCK_EX);
}
function log_error($section, $msg, $ctx = []) { log_write('ERROR', $section, $msg, $ctx); }
function log_info($section, $msg, $ctx = []) { log_write('INFO', $section, $msg, $ctx); }

// Sends every PHP error and uncaught exception to the section's own log.
function init_section($section) {
    ini_set('display_errors', '0');
    set_error_handler(function ($no, $str, $file, $line) use ($section) {
        log_error($section, "PHP $no: $str", ['file' => basename($file), 'line' => $line]);
        return true;
    });
    set_exception_handler(function ($ex) use ($section) {
        log_error($section, get_class($ex) . ': ' . $ex->getMessage(), ['file' => basename($ex->getFile()), 'line' => $ex->getLine()]);
        http_response_code(500);
        if (function_exists('is_ajax') && is_ajax()) {
            header('Content-Type: application/json');
            echo json_encode(['ok' => false, 'message' => 'Something went wrong. The error was logged.']);
        } else {
            echo 'Something went wrong. The error was logged.';
        }
    });
}
