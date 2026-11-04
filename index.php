<?php
require __DIR__ . '/core/bootstrap.php';
init_section('auth');
start_session();
$u = current_user();
header('Location: ' . ($u ? dashboard_url($u['role']) : url('auth/login.php')));
