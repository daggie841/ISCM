<?php
require_once __DIR__ . '/menu.php';

function page_header($title, $active = '') {
    $u = current_user();
    $app = app_config('app_name');
    echo '<!doctype html><html lang="en"><head><meta charset="utf-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<meta name="csrf" content="' . e(csrf_token()) . '">';
    echo '<meta name="base" content="' . e(url()) . '">';
    echo '<title>' . e($title) . ' | ' . e($app) . '</title>';
    echo '<link rel="stylesheet" href="' . e(url('assets/css/main.css')) . '"></head><body>';
    echo '<div class="shell"><aside class="side"><div class="brand">' . e($app) . '</div><nav>';
    foreach (menu_for($u['role']) as $item) {
        $cls = ($item[0] === $active) ? ' class="on"' : '';
        echo '<a' . $cls . ' href="' . e(url($item[1])) . '">' . e($item[0]) . '</a>';
    }
    echo '</nav></aside><div class="main"><header class="top"><h1>' . e($title) . '</h1>';
    echo '<div class="who">' . e($u['name']) . ' (' . e($u['role']) . ') ';
    echo '<button type="button" class="btn small ghost" id="logout-btn">Log out</button></div></header><main class="content">';
}

function page_footer(array $scripts = []) {
    echo '</main></div></div><div id="toast" class="toast" role="status"></div>';
    echo '<script src="' . e(url('assets/js/common.js')) . '"></script>';
    foreach ($scripts as $s) echo '<script src="' . e(url($s)) . '"></script>';
    echo '</body></html>';
}

// Simple centered page for login and related screens.
function auth_header($title) {
    echo '<!doctype html><html lang="en"><head><meta charset="utf-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<meta name="csrf" content="' . e(csrf_token()) . '"><meta name="base" content="' . e(url()) . '">';
    echo '<title>' . e($title) . ' | ' . e(app_config('app_name')) . '</title>';
    echo '<link rel="stylesheet" href="' . e(url('assets/css/main.css')) . '"></head><body class="authbody">';
    echo '<div class="authcard"><div class="brand dark">' . e(app_config('app_name')) . '</div><h1>' . e($title) . '</h1>';
}
function auth_footer(array $scripts = []) {
    echo '</div><div id="toast" class="toast" role="status"></div>';
    echo '<script src="' . e(url('assets/js/common.js')) . '"></script>';
    foreach ($scripts as $s) echo '<script src="' . e(url($s)) . '"></script>';
    echo '</body></html>';
}
