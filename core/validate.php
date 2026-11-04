<?php
function valid_email($v) { return filter_var($v, FILTER_VALIDATE_EMAIL) !== false && strlen($v) <= 150; }
function valid_password($v) { return strlen($v) >= 8 && preg_match('/[A-Za-z]/', $v) && preg_match('/\d/', $v); }
function str_cut($v, $max) { return function_exists('mb_substr') ? mb_substr((string)$v, 0, $max) : substr((string)$v, 0, $max); }
function clean_str($v, $max = 150) { return str_cut(trim((string)$v), $max); }
