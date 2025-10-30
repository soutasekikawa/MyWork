<?php
// modules/sql_injection/index.php
// モジュール入り口ページ（ディレクトリ一覧表示の代わりに表示される）
require_once __DIR__ . '/../../include/header.php';
require_once __DIR__ . '/../../include/utils.php';

// 現在のモードがクエリで指定されていればそれを使って遷移する仕組みも可能
$mode = get_mode('vulnerable'); // vulnerable or secure
?>
<div class="container mt-4 module-entry cyber-panel">
  <h2>SQLインジェクション モジュール</h2>
  <p>このモジュールでは、脆弱版（攻撃体験）と安全版（防御学習）を比較できます。</p>

  <div class="row">
    <div class="col-md-6 mb-3">
      <div class="card card-dark">
        <div class="card-body">
          <h5 class="card-title">攻撃モード（脆弱）</h5>
          <p>脆弱な実装を体験し、攻撃ベクターを確認します。</p>
          <a class="btn btn-danger" href="vulnerable.php">攻撃モードを開く</a>
        </div>
      </div>
    </div>

    <div class="col-md-6 mb-3">
      <div class="card card-dark">
        <div class="card-body">
          <h5 class="card-title">防御モード（安全）</h5>
          <p>安全な実装例を確認し、修正方法を学びます。</p>
          <a class="btn btn-success" href="secure.php">防御モードを開く</a>
        </div>
      </div>
    </div>
  </div>

  <div class="mt-3">
    <details>
      <summary>💡 ヒント／説明を表示</summary>
      <p>各ページには「解説」セクションを置き、攻撃の仕組みと防御方法を日本語で説明してください。</p>
    </details>
  </div>

  <a class="btn btn-link mt-3" href="/SecuLearn/index.php">&larr; モジュール一覧へ戻る</a>
</div>

<?php require_once __DIR__ . '/../../include/footer.php'; ?>
