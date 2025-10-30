<?php
// DB 接続ユーティリティ
session_start();

// ※ 実際の環境に合わせて書き換えてください
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'seculearn');

function connect_db() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_errno) {
        // 簡易エラー表示（開発用）
        die("DB接続エラー: " . $conn->connect_error);
    }
    $conn->set_charset("utf8mb4");
    return $conn;
}
