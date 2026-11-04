<?php
function db() {
    static $pdo;
    if ($pdo) return $pdo;
    $c = require dirname(__DIR__) . '/config/db.php';
    try {
        $pdo = new PDO("mysql:host={$c['host']};dbname={$c['name']};charset={$c['charset']}", $c['user'], $c['pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    } catch (PDOException $ex) {
        log_error('database', 'Connect failed: ' . $ex->getMessage());
        throw new RuntimeException('Database unavailable');
    }
    return $pdo;
}
function db_query($sql, $params = []) {
    try {
        $st = db()->prepare($sql);
        $st->execute($params);
        return $st;
    } catch (PDOException $ex) {
        log_error('database', 'Query failed: ' . $ex->getMessage(), ['sql' => substr($sql, 0, 200)]);
        throw $ex;
    }
}
function db_all($sql, $params = []) { return db_query($sql, $params)->fetchAll(); }
function db_one($sql, $params = []) { return db_query($sql, $params)->fetch() ?: null; }
function db_val($sql, $params = []) { $r = db_query($sql, $params)->fetchColumn(); return $r === false ? null : $r; }
function db_insert($sql, $params = []) { db_query($sql, $params); return (int)db()->lastInsertId(); }
