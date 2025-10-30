<?php
require_once __DIR__ . '/include/header.php';
require_once __DIR__ . '/include/db_connect.php';
require_once __DIR__ . '/include/utils.php';

$conn = connect_db();
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = $_POST['username'] ?? '';
    $p = $_POST['password'] ?? '';

    // デモ用：平文比較（本番ではハッシュを使う）
    $stmt = $conn->prepare("SELECT id, username FROM users WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $u, $p);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $_SESSION['username'] = $row['username'];
        write_log($row['username'], 'ログイン成功 (login.php)');
        header('Location: index.php');
        exit;
    } else {
        $err = 'ユーザー名またはパスワードが違います。';
        write_log($u, 'ログイン失敗 (login.php)');
    }
}
?>
<div class="container mt-4">
  <h2>簡易ログイン（学習用）</h2>
  <?php if ($err): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($err); ?></div>
  <?php endif; ?>
  <form method="POST" action="login.php">
    <div class="mb-3">
      <label class="form-label">ユーザー名</label>
      <input class="form-control" name="username" required>
    </div>
    <div class="mb-3">
      <label class="form-label">パスワード</label>
      <input class="form-control" type="password" name="password" required>
    </div>
    <button class="btn btn-primary" type="submit">ログイン</button>
    <a href="index.php" class="btn btn-link">戻る</a>
  </form>
</div>
<?php require_once __DIR__ . '/include/footer.php'; ?>
