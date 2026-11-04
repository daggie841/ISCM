<?php
require __DIR__ . '/../../core/bootstrap.php';
init_section('auth');
start_session();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_fail('Invalid request.', 405);
csrf_check();
if (current_user()) log_activity('logout');
session_unset();
session_destroy();
json_ok(['redirect' => url('auth/login.php')]);
