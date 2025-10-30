<?php
// CSRF 安全版サンプル：セッション内トークンでチェック
require_once __DIR__ . '/../../include/header.php';
require_once __DIR__ . '/../../include/db_connect.php';
require_once __DIR__ . '/../../include/utils.php';

$conn = connect_db();

// トークン生成
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
}
$token = $_SESSION['csrf_token'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $posted_token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($token, $posted_token)) {
        $err = 'CSRF トークンが無効です。';
        write_log($_SESSION['username'] ?? 'guest', 'CSRF トークン無効');
    } else {
        $username = $_POST['username'] ?? '';
        $bio = $_POST['bio'] ?? '';
        if ($username && $bio !== null) {
            $stmt = $conn->prepare("UPDATE users SET bio = ? WHERE username = ?");
            $stmt->bind_param("ss", $bio, $username);
            $stmt->execute();
            write_log($username, 'CSRF安全ページでプロフィール更新: ' . substr($bio, 0, 100));
            $msg = 'プロフィールを更新しました（安全版）。';
        }
    }
}
?>
<div class="container mt-4">
  <h2>CSRF 安全版（secure）</h2>
  <?php if (!empty($err)): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($err); ?></div>
  <?php endif; ?>
  <?php if (!empty($msg)): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($msg); ?></div>
  <?php endif; ?>

  <form method="POST" action="secure.php">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($token); ?>">
    <div class="mb-3">
      <label class="form-label">ユーザー名</label>
      <input class="form-control" name="username" required>
    </div>
    <div class="mb-3">
      <label class="form-label">自己紹介（bio）</label>
      <textarea class="form-control" name="bio" rows="3"></textarea>
    </div>
    <button class="btn btn-success" type="submit">プロフィール更新（安全）</button>
    <a class="btn btn-secondary" href="../csrf/">戻る</a>
  </form>

  <details class="mt-3">
    <summary>💡 解説（なぜ安全か）</summary>
    <p>フォームにセッション内のトークンを含め、受け取ったトークンと比較することで外部からの偽造リクエストを防ぎます。</p>
  </details>
</div>
<?php require_once __DIR__ . '/../../include/footer.php'; ?>
