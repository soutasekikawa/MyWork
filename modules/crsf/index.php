<?php
// modules/csrf/demo.php
// CSRFデモンストレーター
require_once __DIR__ . '/../../include/header.php';
require_once __DIR__ . '/../../include/db_connect.php';
require_once __DIR__ . '/../../include/utils.php';

$conn = connect_db();

// デモ用のユーザープロフィール表示
$demo_user = 'demo_user';
$stmt = $conn->prepare("INSERT INTO users (username) VALUES (?)");
if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
}

$name = 'demo_user';
$stmt->bind_param("s", $name);

$stmt->bind_param("s", $demo_user);
$stmt->execute();
$result = $stmt->get_result();
$current_bio = '';
if ($row = $result->fetch_assoc()) {
    $current_bio = $row['bio'];
}

// プロフィール更新処理（脆弱版をシミュレート）
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_bio = $_POST['bio'] ?? '';
    $stmt = $conn->prepare("UPDATE users SET bio = ? WHERE username = ?");
    $stmt->bind_param("ss", $new_bio, $demo_user);
    $stmt->execute();
    write_log($_SESSION['username'] ?? 'guest', "CSRFデモ: プロフィール更新 -> " . substr($new_bio, 0, 50));
    $current_bio = $new_bio;
    $success_msg = "プロフィールを更新しました！";
}

?>
<div class="container mt-4">
  <div class="cyber-panel">
    <h1>🎮 CSRFデモンストレーター</h1>
    <p class="lead">実際のCSRF攻撃の流れを安全に体験しましょう</p>

    <div class="row">
      <div class="col-md-6">
        <div class="card card-dark">
          <div class="card-body">
            <h4>🔓 脆弱なプロフィール更新</h4>
            <p>現在のプロフィール: <strong><?php echo htmlspecialchars($current_bio ?: '（未設定）'); ?></strong></p>
            
            <?php if (isset($success_msg)): ?>
              <div class="alert alert-success"><?php echo htmlspecialchars($success_msg); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="demo.php">
              <div class="mb-3">
                <label class="form-label">新しいプロフィール</label>
                <input type="text" class="form-control" name="bio" placeholder="自己紹介を入力..." required>
              </div>
              <button type="submit" class="btn btn-warning">プロフィール更新（CSRF脆弱）</button>
            </form>
            
            <div class="mt-3">
              <small class="text-warning">※ このフォームにはCSRF対策がありません</small>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-md-6">
        <div class="card card-dark">
          <div class="card-body">
            <h4>🛡️ CSRF攻撃シミュレーション</h4>
            <p>悪意のあるサイトから送信される偽のリクエスト：</p>
            
            <div class="mb-3">
              <h6>HTMLフォームを使った攻撃</h6>
              <pre class="bg-dark text-light p-2 small"><code>&lt;form action="http://localhost/SecuLearn/modules/csrf/demo.php" method="POST"&gt;
  &lt;input type="hidden" name="bio" value="私はハッキングされました"&gt;
  &lt;input type="submit" value="クリックしてください"&gt;
&lt;/form&gt;</code></pre>
            </div>
            
            <div class="mb-3">
              <h6>JavaScriptを使った自動送信</h6>
              <pre class="bg-dark text-light p-2 small"><code>&lt;script&gt;
fetch('http://localhost/SecuLearn/modules/csrf/demo.php', {
  method: 'POST',
  body: 'bio=自動で変更されました'
});
&lt;/script&gt;</code></pre>
            </div>
            
            <div class="mb-3">
              <h6>画像タグを使ったGETリクエスト</h6>
              <pre class="bg-dark text-light p-2 small"><code>&lt;img src="http://localhost/SecuLearn/modules/csrf/demo.php?bio=画像で変更"&gt;</code></pre>
            </div>
            
            <div class="alert alert-info">
              <strong>💡 試してみよう:</strong> 別タブでこのページを開き、フォームを送信してみましょう。ログイン状態が維持されているため、プロフィールが更新されます。
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row mt-4">
      <div class="col-12">
        <div class="card card-dark">
          <div class="card-body">
            <h4>🔍 防御方法の比較</h4>
            <div class="row">
              <div class="col-md-6">
                <h5 class="text-danger">❌ 脆弱な実装</h5>
                <ul>
                  <li>CSRFトークンなし</li>
                  <li>SameSite Cookie未設定</li>
                  <li>リファラチェックなし</li>
                  <li>重要な操作でも再認証なし</li>
                </ul>
              </div>
              <div class="col-md-6">
                <h5 class="text-success">✅ 安全な実装</h5>
                <ul>
                  <li>セッションベースのCSRFトークン</li>
                  <li>SameSite=Strict Cookie</li>
                  <li>二重送信Cookieパターン</li>
                  <li>重要な操作での再認証</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="mt-4">
      <a href="index.php" class="btn btn-secondary">← CSRFモジュールに戻る</a>
      <a href="vulnerable.php" class="btn btn-danger">脆弱版を詳しく見る</a>
      <a href="secure.php" class="btn btn-success">安全版を詳しく見る</a>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../../include/footer.php'; ?>