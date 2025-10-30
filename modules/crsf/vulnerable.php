<?php
// CSRF 脆弱版サンプル：トークンチェックがない状態で状態変化を行う
require_once __DIR__ . '/../../include/header.php';
require_once __DIR__ . '/../../include/db_connect.php';
require_once __DIR__ . '/../../include/utils.php';

$conn = connect_db();

// デモ：ユーザーの「プロフィール説明」を更新するフォーム（CSRF 脆弱）
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $bio = $_POST['bio'] ?? '';
    if ($username && $bio !== null) {
        $stmt = $conn->prepare("UPDATE users SET bio = ? WHERE username = ?");
        $stmt->bind_param("ss", $bio, $username);
        $stmt->execute();
        write_log($username, 'CSRF脆弱ページでプロフィール更新: ' . substr($bio, 0, 100));
        $msg = 'プロフィールを更新しました（脆弱版）。';
    }
}
?>
<div class="container mt-4">
  <h2>CSRF 脆弱版（vulnerable）</h2>
  <p>このフォームには CSRF 対策が施されていません。別サイトから自動で送信されると危険です。</p>
  <?php if (!empty($msg)): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($msg); ?></div>
  <?php endif; ?>
  <form method="POST" action="vulnerable.php">
    <div class="mb-3">
      <label class="form-label">ユーザー名</label>
      <input class="form-control" name="username" required>
    </div>
    <div class="mb-3">
      <label class="form-label">自己紹介（bio）</label>
      <textarea class="form-control" name="bio" rows="3"></textarea>
    </div>
    <button class="btn btn-danger" type="submit">プロフィール更新（脆弱）</button>
    <a class="btn btn-secondary" href="../csrf/">戻る</a>
  </form>

  <details class="mt-3">
    <summary>💡 解説（なぜ危険か）</summary>
    <p>トークンがないため、攻撃者が外部ページから自動で POST させることにより、意図しない操作が行われる可能性があります。</p>
  </details>
</div>
<?php require_once __DIR__ . '/../../include/footer.php'; ?>
