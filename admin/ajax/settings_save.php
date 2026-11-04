<?php
require __DIR__ . '/../../core/bootstrap.php';
guard_ajax('admin', ['admin']);
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_fail('Invalid request.', 405);

$keys = require ROOT . '/core/models/setting_keys.php';
$in = request_input();
$clean = [];
foreach ($in as $k => $v) {
    if (!isset($keys[$k])) continue;
    [$type, $secret] = $keys[$k];
    $v = trim((string)$v);
    if ($secret && $v === '********') continue; // unchanged secret
    switch ($type) {
        case 'int':   if (!ctype_digit($v) || (int)$v > 100000) json_fail("$k must be a whole number."); break;
        case 'bool':  $v = $v === '1' ? '1' : '0'; break;
        case 'email': if ($v !== '' && !valid_email($v)) json_fail('Enter a valid sender email.'); break;
        case 'time':  if (!preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $v)) json_fail('Schedule must look like 02:00.'); break;
        case 'horizons':
            $parts = array_filter(array_map('trim', explode(',', $v)), 'strlen');
            foreach ($parts as $h) if (!in_array($h, ['7', '30', '90'], true)) json_fail('Horizons can only be 7, 30 and 90.');
            if (!$parts) json_fail('Choose at least one horizon.');
            $v = implode(',', $parts);
            break;
        default:      $v = str_cut($v, 200);
    }
    if ($k === 'session_timeout_min' && ((int)$v < 5 || (int)$v > 480)) json_fail('Session timeout must be 5 to 480 minutes.');
    $clean[$k] = $v;
}
if (!$clean) json_fail('Nothing to save.');
foreach ($clean as $k => $v) setting_save($k, $v);
log_activity('settings_saved', implode(',', array_keys($clean)));
json_ok(['message' => 'Settings saved.']);
