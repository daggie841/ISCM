<?php
require __DIR__ . '/../../core/bootstrap.php';
guard_ajax('admin', ['admin']);

$q = clean_str($_GET['q'] ?? '', 100);
$role = clean_str($_GET['role'] ?? '', 30);
$status = clean_str($_GET['status'] ?? '', 20);
$page = max(1, (int)($_GET['page'] ?? 1));
$per = 15;

$where = ["u.status <> 'pending'"];
$p = [];
if ($q !== '') { $where[] = '(u.name LIKE ? OR u.email LIKE ?)'; $p[] = "%$q%"; $p[] = "%$q%"; }
if ($role !== '') { $where[] = 'r.name = ?'; $p[] = $role; }
if (in_array($status, ['active', 'disabled'], true)) { $where[] = 'u.status = ?'; $p[] = $status; }
$w = implode(' AND ', $where);

$total = (int)db_val("SELECT COUNT(*) FROM users u LEFT JOIN roles r ON r.id = u.role_id WHERE $w", $p);
$rows = db_all("SELECT u.id, u.name, u.email, u.status, u.last_login, r.name AS role, r.label AS role_label
                FROM users u LEFT JOIN roles r ON r.id = u.role_id WHERE $w
                ORDER BY u.name LIMIT $per OFFSET " . (($page - 1) * $per), $p);
$pending = db_all("SELECT id, name, email, created_at FROM users WHERE status = 'pending' ORDER BY created_at");
$roles = db_all('SELECT id, name, label FROM roles ORDER BY id');
json_ok(['users' => $rows, 'total' => $total, 'page' => $page, 'per' => $per, 'pending' => $pending, 'roles' => $roles, 'me' => (int)$_SESSION['user_id']]);
