<?php
// 共通ユーティリティ関数

require_once __DIR__ . '/db_connect.php';

/**
 * write_log: 学習ログを logs テーブルに書き込む
 * @param string $user
 * @param string $action
 */
function write_log($user, $action) {
    $conn = connect_db();
    $stmt = $conn->prepare("INSERT INTO logs (username, action) VALUES (?, ?)");
    if ($stmt) {
        $stmt->bind_param("ss", $user, $action);
        $stmt->execute();
        $stmt->close();
    }
    $conn->close();
}

/**
 * is_admin: 簡易管理者チェック（デモ用: username == 'admin'）
 */
function is_admin() {
    return isset($_SESSION['username']) && $_SESSION['username'] === 'admin';
}

/**
 * get_mode: GET/POST で渡された mode パラメータを安全に取得
 */
function get_mode($default = 'vulnerable') {
    $mode = $_GET['mode'] ?? $_POST['mode'] ?? $default;
    if (!in_array($mode, ['vulnerable', 'secure'])) $mode = $default;
    return $mode;
}
