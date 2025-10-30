<?php
// 共通ヘッダー（Bootstrap 読み込み等）
?>
<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SecuLearn</title>
  <link href="assets/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="/index.php">SecuLearn</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="index.php">ホーム</a></li>
        <li class="nav-item"><a class="nav-link" href="login.php">簡易ログイン</a></li>
        <li class="nav-item"><a class="nav-link" href="csrf.php">CSRF</a></li>
      </ul>
      <ul class="navbar-nav">
        <?php if (isset($_SESSION['username'])): ?>
          <li class="nav-item"><span class="nav-link">ユーザー: <?php echo htmlspecialchars($_SESSION['username']); ?></span></li>
          <li class="nav-item"><a class="nav-link" href="admin/index.php">管理</a></li>
          <li class="nav-item"><a class="nav-link" href="logout.php">ログアウト</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="login.php">ログイン</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
