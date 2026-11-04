<?php
require __DIR__ . '/../../core/bootstrap.php';
guard_ajax('admin', ['admin']);
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_fail('Invalid request.', 405);

$in = request_input();
$id = (int)($in['id'] ?? 0);
$name = clean_str($in['name'] ?? '', 80);
if ($name === '') json_fail('Enter a category name.');
if (db_one('SELECT id FROM categories WHERE name = ? AND id <> ?', [$name, $id])) json_fail('That category already exists.');

if ($id) db_query('UPDATE categories SET name = ? WHERE id = ?', [$name, $id]);
else db_insert('INSERT INTO categories (name) VALUES (?)', [$name]);
log_activity('category_saved', $name);
json_ok(['message' => 'Category saved.']);
