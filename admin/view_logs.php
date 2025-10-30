<?php
require_once __DIR__ . '/../include/header.php';
require_once __DIR__ . '/../include/db_connect.php';
require_once __DIR__ . '/../include/utils.php';

if (!is_admin()) {
    echo '<div class="container mt-4"><div class="alert alert-danger">管理者権限が必要です。</div></div>';
    require_once __DIR__ . '/../include/footer.php';
    exit;
}

$conn = connect_db();
$res = $conn->query("SELECT id, username, action, created_at FROM logs ORDER BY created_at DESC LIMIT 1000");

?>
<div class="container mt-4">
  <h2>全ログ（最大1000件）</h2>
  <table class="table table-sm table-striped">
    <thead><tr><th>#</th><th>ユーザー</th><th>アクション</th><th>日時</th></tr></thead>
    <tbody>
      <?php while ($row = $res->fetch_assoc()): ?>
        <tr>
          <td><?php echo $row['id']; ?></td>
          <td><?php echo htmlspecialchars($row['username']); ?></td>
          <td><?php echo htmlspecialchars($row['action']); ?></td>
          <td><?php echo $row['created_at']; ?></td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
  <a class="btn btn-primary" href="index.php">戻る</a>
</div>
<?php require_once __DIR__ . '/../include/footer.php'; ?>
