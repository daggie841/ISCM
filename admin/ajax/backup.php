<?php
require __DIR__ . '/../../core/bootstrap.php';
guard_ajax('admin', ['admin']);
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_fail('Invalid request.', 405);

$dir = ROOT . '/backups';
if (!is_dir($dir)) mkdir($dir, 0775, true);
$file = 'backup_' . date('Ymd_His') . '.sql';
$fh = fopen("$dir/$file", 'w');
if (!$fh) { log_error('admin', 'Cannot write backup file'); json_fail('Cannot write to the backups folder.', 500); }

$pdo = db();
fwrite($fh, "SET FOREIGN_KEY_CHECKS=0;\n");
foreach ($pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN) as $t) {
    $create = $pdo->query("SHOW CREATE TABLE `$t`")->fetch(PDO::FETCH_NUM)[1];
    fwrite($fh, "DROP TABLE IF EXISTS `$t`;\n$create;\n");
    $st = $pdo->query("SELECT * FROM `$t`");
    while ($row = $st->fetch(PDO::FETCH_NUM)) {
        $vals = array_map(fn($v) => $v === null ? 'NULL' : $pdo->quote((string)$v), $row);
        fwrite($fh, "INSERT INTO `$t` VALUES (" . implode(',', $vals) . ");\n");
    }
}
fwrite($fh, "SET FOREIGN_KEY_CHECKS=1;\n");
fclose($fh);
log_activity('backup_created', $file);
json_ok(['file' => $file, 'message' => 'Backup created.']);
