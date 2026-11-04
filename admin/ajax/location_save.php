<?php
require __DIR__ . '/../../core/bootstrap.php';
guard_ajax('admin', ['admin']);
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_fail('Invalid request.', 405);

$in = request_input();
$id = (int)($in['id'] ?? 0);
$name = clean_str($in['name'] ?? '', 80);
$addr = clean_str($in['address'] ?? '', 200);
$status = ($in['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active';
if ($name === '') json_fail('Enter a location name.');
if (db_one('SELECT id FROM locations WHERE name = ? AND id <> ?', [$name, $id])) json_fail('A location with that name exists.');

if ($id) db_query('UPDATE locations SET name = ?, address = ?, status = ? WHERE id = ?', [$name, $addr, $status, $id]);
else db_insert('INSERT INTO locations (name, address, status) VALUES (?, ?, ?)', [$name, $addr, $status]);
log_activity('location_saved', $name);
json_ok(['message' => 'Location saved.']);
