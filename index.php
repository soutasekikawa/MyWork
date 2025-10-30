<?php
// SecuLearn トップページ
require_once __DIR__ . '/include/header.php';
?>
<div class="container mt-4">
  <h1>SecuLearn - 脆弱性学習プラットフォーム（日本語版）</h1>
  <p class="lead">この環境は教育目的のために脆弱性を意図的に残したページと、その安全版を比較して学べます。</p>

  <div class="card mb-3">
    <div class="card-body">
      <h5 class="card-title">モジュール一覧</h5>
      <ul>
        <li><a href="modules/sql_injection/">SQLインジェクション（モジュール）</a>（※詳細は modules/sql_injection 配下）</li>
        <li><a href="modules/xss/">XSS（モジュール）</a>（※詳細は modules/xss 配下）</li>
        <li><a href="modules/csrf/">CSRF（モジュール）</a></li>
      </ul>
      <p>右上のメニューから管理画面にもアクセスできます（admin/admin にログイン）。</p>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <h5 class="card-title">使い方（簡単）</h5>
      <ol>
        <li>database/init.sql を DB に反映（手動で実行）</li>
        <li>config を <code>include/db_connect.php</code> に合わせる</li>
        <li>各モジュールを開いて「脆弱（vulnerable）」と「安全（secure）」を比較</li>
      </ol>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/include/footer.php'; ?>
