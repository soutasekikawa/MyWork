<?php
require_once __DIR__ . '/include/header.php';
?>
<div class="container mt-4">
  <h2>CSRF（クロスサイトリクエストフォージェリ）モジュール</h2>
  <p>学習用 CSRF ページへ移動します。</p>
  <a class="btn btn-primary" href="modules/csrf/vulnerable.php">攻撃（脆弱）モード</a>
  <a class="btn btn-success" href="modules/csrf/secure.php">防御（安全）モード</a>
</div>
<?php require_once __DIR__ . '/include/footer.php'; ?>
