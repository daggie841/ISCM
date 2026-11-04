<?php
function json_ok($data = []) {
    header('Content-Type: application/json');
    echo json_encode(['ok' => true, 'data' => $data]);
    exit;
}
function json_fail($message, $code = 400) {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode(['ok' => false, 'message' => $message]);
    exit;
}
// Reads a JSON body, falling back to normal POST fields.
function request_input() {
    $raw = file_get_contents('php://input');
    $j = $raw ? json_decode($raw, true) : null;
    return is_array($j) ? $j : $_POST;
}
