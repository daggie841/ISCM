<?php
require __DIR__ . '/../../core/bootstrap.php';
guard_ajax('admin', ['admin']);

$uid = (int)($_GET['user'] ?? 0);
$action = clean_str($_GET['action'] ?? '', 60);
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$per = 25;

$where = ['1=1'];
$p = [];
if ($uid) { $where[] = 'a.user_id = ?'; $p[] = $uid; }
if ($action !== '') { $where[] = 'a.action LIKE ?'; $p[] = "%$action%"; }
if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $from)) { $where[] = 'a.created_at >= ?'; $p[] = "$from 00:00:00"; }
if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $to)) { $where[] = 'a.created_at <= ?'; $p[] = "$to 23:59:59"; }
$w = implode(' AND ', $where);

$total = (int)db_val("SELECT COUNT(*) FROM activity_log a WHERE $w", $p);
$rows = db_all("SELECT a.created_at, COALESCE(u.name, '-') AS user, a.action, a.details, a.ip
                FROM activity_log a LEFT JOIN users u ON u.id = a.user_id WHERE $w
                ORDER BY a.id DESC LIMIT $per OFFSET " . (($page - 1) * $per), $p);
$users = db_all('SELECT id, name FROM users ORDER BY name');
json_ok(['rows' => $rows, 'total' => $total, 'page' => $page, 'per' => $per, 'users' => $users]);
