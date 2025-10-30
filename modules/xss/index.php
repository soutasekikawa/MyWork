<?php
// modules/xss/game.php
// XSS Learning Game - 安全な学習環境
require_once __DIR__ . '/../../include/header.php';
require_once __DIR__ . '/../../include/utils.php';

// セッションは既に開始されているので、session_start()は不要

// 基本的なセキュリティヘッダ
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data:;");
header('Referrer-Policy: no-referrer');

// ヘルパー: HTML エスケープ
function h($s) {
    return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// レベル定義
$levels = [
    1 => [
        'title' => 'HTML本文に直接挿入されるケース',
        'description' => 'HTML の本文にユーザー入力がそのまま置かれると、スクリプトが実行される可能性があります。',
        'example' => '<code>&lt;div&gt;ユーザー入力&lt;/div&gt;</code>',
        'hint' => '&lt;script&gt; タグや &lt;img onerror=...&gt; を試してみましょう。',
        'context' => 'html_body'
    ],
    2 => [
        'title' => '属性値（例: value="..."）に入るケース',
        'description' => '属性値の文脈では、引用符を使ったエスケープが重要です。',
        'example' => '<code>&lt;input value="ユーザー入力"/&gt;</code>',
        'hint' => '" を挿入すると属性が壊れる可能性があります。',
        'context' => 'attribute'
    ],
    3 => [
        'title' => 'JavaScript 内に埋め込まれるケース',
        'description' => 'スクリプト内に文字列として埋められる場合、特殊文字で文脈が壊れます。',
        'example' => '<code>&lt;script&gt;var x = "ユーザー入力";&lt;/script&gt;</code>',
        'hint' => '改行や \\ などに気をつけてください。',
        'context' => 'javascript'
    ],
    4 => [
        'title' => 'URLコンテキスト（href属性など）',
        'description' => 'URLとして扱われる場合、javascript: スキームの危険性があります。',
        'example' => '<code>&lt;a href="ユーザー入力"&gt;リンク&lt;/a&gt;</code>',
        'hint' => 'javascript:alert(1) を試してみましょう',
        'context' => 'url'
    ]
];

// ゲーム用セッション変数の初期化
if (!isset($_SESSION['xss_game_score'])) {
    $_SESSION['xss_game_score'] = 0;
}
if (!isset($_SESSION['xss_game_attempts'])) {
    $_SESSION['xss_game_attempts'] = 0;
}
if (!isset($_SESSION['xss_game_completed'])) {
    $_SESSION['xss_game_completed'] = [];
}

// リセット処理
if (isset($_GET['reset'])) {
    $_SESSION['xss_game_score'] = 0;
    $_SESSION['xss_game_attempts'] = 0;
    $_SESSION['xss_game_completed'] = [];
    header('Location: game.php');
    exit;
}

// 現在のレベル
$level = isset($_GET['level']) ? max(1, min(count($levels), (int)$_GET['level'])) : 1;

$submitted = false;
$payload = '';
$feedback = '';
$escaped = '';
$simulated = '';
$is_dangerous = false;
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submitted = true;
    $payload = isset($_POST['payload']) ? (string)$_POST['payload'] : '';
    $_SESSION['xss_game_attempts']++;

    // 常に安全に扱う: 出力はエスケープして表示する
    $escaped = h($payload);

    // 危険性の検出
    $is_dangerous = false;
    $danger_patterns = [
        '/<script/i',
        '/on\w+\s*=/i',
        '/javascript:/i',
        '/vbscript:/i',
        '/data:/i',
    ];
    
    foreach ($danger_patterns as $pattern) {
        if (preg_match($pattern, $payload)) {
            $is_dangerous = true;
            break;
        }
    }

    // シミュレーション表示
    $simulated = $payload;
    
    // 危険なタグを視覚化
    $simulated = preg_replace_callback('#<(/?)(script|iframe|object|embed|svg|math|body|head|link|style)([^>]*)>#i', function($m){
        return '🚨[TAG:' . h($m[1] . $m[2] . $m[3]) . ']';
    }, $simulated);
    
    // イベントハンドラを視覚化
    $simulated = preg_replace_callback('/on[a-zA-Z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]*)/i', function($m){
        return '⚠️[EVENT:' . h($m[0]) . ']';
    }, $simulated);
    
    // JavaScript URIを視覚化
    $simulated = preg_replace_callback('/(javascript|vbscript|data):/i', function($m){
        return '🔴[SCHEME:' . h($m[0]) . ']';
    }, $simulated);
    
    $simulated = h($simulated);

    // フィードバック生成（教育目的）
    $score_delta = 0;
    $current_context = $levels[$level]['context'];
    
    switch ($current_context) {
        case 'html_body':
            if (preg_match('/<script/i', $payload)) {
                $feedback = '✅ <strong>発見!</strong> &lt;script&gt; タグはHTML本文で実行される可能性があります。';
                $score_delta = 3;
                $success = true;
            } elseif (preg_match('/<img[^>]*onerror/i', $payload)) {
                $feedback = '✅ <strong>発見!</strong> imgタグのonerrorイベントも危険です。';
                $score_delta = 2;
                $success = true;
            } elseif (preg_match('/</', $payload)) {
                $feedback = 'HTMLタグを含んでいますが、より効果的なペイロードを探してみましょう。';
                $score_delta = 1;
            } else {
                $feedback = '安全なテキストです。XSSを発動させるタグやイベントを試してみましょう。';
                $score_delta = 0;
            }
            break;
            
        case 'attribute':
            if (preg_match('/"\s*>/', $payload)) {
                $feedback = '✅ <strong>発見!</strong> 属性を閉じて新しいタグを挿入できます！';
                $score_delta = 3;
                $success = true;
            } elseif (preg_match('/"/', $payload)) {
                $feedback = '引用符で属性を閉じることに成功しました！';
                $score_delta = 2;
                $success = true;
            } else {
                $feedback = '属性値の中でスクリプトを実行するには、まず属性を閉じる必要があります。';
                $score_delta = 0;
            }
            break;
            
        case 'javascript':
            if (preg_match('/["\']\s*\)\s*;?\s*\/\//', $payload)) {
                $feedback = '✅ <strong>発見!</strong> JavaScriptの文字列を閉じてコードを実行できます！';
                $score_delta = 3;
                $success = true;
            } elseif (preg_match('/["\']\s*\)/', $payload)) {
                $feedback = 'JavaScriptの文脈を閉じることに成功しました！';
                $score_delta = 2;
                $success = true;
            } else {
                $feedback = 'JavaScript内では、文字列を閉じて新しい文を実行する方法を試してみましょう。';
                $score_delta = 0;
            }
            break;
            
        case 'url':
            if (preg_match('/javascript:/i', $payload)) {
                $feedback = '✅ <strong>発見!</strong> javascript: スキームでスクリプトを実行できます！';
                $score_delta = 3;
                $success = true;
            } else {
                $feedback = 'URLコンテキストでは javascript: スキームが危険です。';
                $score_delta = 0;
            }
            break;
    }

    $_SESSION['xss_game_score'] += $score_delta;
    
    // レベルクリア記録
    if ($success && !in_array($level, $_SESSION['xss_game_completed'])) {
        $_SESSION['xss_game_completed'][] = $level;
        write_log($_SESSION['username'] ?? 'guest', "XSSゲームレベル{$level}クリア");
    }
}

// 進捗計算
$progress = count($_SESSION['xss_game_completed']) / count($levels) * 100;
?>

<div class="container mt-4">
  <div class="cyber-panel">
    <h1>🎯 XSS Learning Game</h1>
    <p class="lead">安全な環境でXSSの仕組みを学びましょう</p>
    
    <!-- 進捗バー -->
    <div class="progress mb-4" style="height: 20px;">
      <div class="progress-bar bg-success" role="progressbar" 
           style="width: <?php echo $progress; ?>%" 
           aria-valuenow="<?php echo $progress; ?>" aria-valuemin="0" aria-valuemax="100">
        <?php echo round($progress); ?>% 完了
      </div>
    </div>

    <div class="row">
      <div class="col-md-8">
        <div class="card card-dark mb-4">
          <div class="card-body">
            <h3>レベル <?php echo $level; ?>: <?php echo $levels[$level]['title']; ?></h3>
            <p><?php echo $levels[$level]['description']; ?></p>
            <div class="alert alert-info">
              <strong>例:</strong> <?php echo $levels[$level]['example']; ?>
            </div>
            <p><strong>💡 ヒント:</strong> <?php echo $levels[$level]['hint']; ?></p>
          </div>
        </div>

        <form method="post" action="?level=<?php echo $level; ?>">
          <div class="mb-3">
            <label class="form-label"><strong>ペイロードを入力:</strong></label>
            <textarea name="payload" class="form-control" rows="4" placeholder="ここにXSSペイロードを入力..." style="font-family: 'Courier New', monospace;"><?php echo isset($_POST['payload']) ? h($_POST['payload']) : ''; ?></textarea>
          </div>
          <button type="submit" class="btn btn-primary">送信して確認</button>
        </form>

        <?php if ($submitted): ?>
          <div class="card card-dark mt-4">
            <div class="card-body">
              <h4>🔍 分析結果</h4>
              
              <div class="mb-3">
                <strong>入力内容:</strong>
                <pre class="bg-dark text-light p-2 rounded"><?php echo $escaped; ?></pre>
              </div>
              
              <div class="mb-3">
                <strong>視覚化:</strong>
                <pre class="bg-dark text-light p-2 rounded"><?php echo $simulated; ?></pre>
              </div>
              
              <div class="alert <?php echo $success ? 'alert-success' : 'alert-warning'; ?>">
                <strong>フィードバック:</strong> <?php echo $feedback; ?>
              </div>
              
              <?php if ($success): ?>
                <div class="alert alert-success">
                  🎉 <strong>成功!</strong> このレベルをクリアしました！
                  <?php if ($level < count($levels)): ?>
                    <a href="?level=<?php echo $level + 1; ?>" class="btn btn-success btn-sm ms-2">次のレベルへ</a>
                  <?php endif; ?>
                </div>
              <?php endif; ?>
            </div>
          </div>
        <?php endif; ?>
      </div>

      <div class="col-md-4">
        <div class="card card-dark">
          <div class="card-body">
            <h5>📊 ゲーム統計</h5>
            <ul class="list-group list-group-flush">
              <li class="list-group-item bg-transparent text-light">
                スコア: <span class="badge bg-primary"><?php echo $_SESSION['xss_game_score']; ?></span>
              </li>
              <li class="list-group-item bg-transparent text-light">
                試行回数: <?php echo $_SESSION['xss_game_attempts']; ?>
              </li>
              <li class="list-group-item bg-transparent text-light">
                完了レベル: <?php echo count($_SESSION['xss_game_completed']); ?>/<?php echo count($levels); ?>
              </li>
            </ul>
          </div>
        </div>

        <div class="card card-dark mt-3">
          <div class="card-body">
            <h5>🎮 レベル選択</h5>
            <div class="d-grid gap-2">
              <?php for ($i = 1; $i <= count($levels); $i++): ?>
                <a href="?level=<?php echo $i; ?>" 
                   class="btn <?php echo in_array($i, $_SESSION['xss_game_completed']) ? 'btn-success' : ($i == $level ? 'btn-primary' : 'btn-outline-primary'); ?>">
                  レベル <?php echo $i; ?>
                  <?php if (in_array($i, $_SESSION['xss_game_completed'])): ?>
                    ✅
                  <?php endif; ?>
                </a>
              <?php endfor; ?>
            </div>
          </div>
        </div>

        <div class="card card-dark mt-3">
          <div class="card-body">
            <h5>🛡️ 安全な学習環境</h5>
            <p class="small">このゲームでは:</p>
            <ul class="small">
              <li>すべての出力は適切にエスケープされます</li>
              <li>スクリプトは実際には実行されません</li>
              <li>危険なパターンを視覚化して表示します</li>
            </ul>
            <a href="?reset=1" class="btn btn-outline-warning btn-sm">ゲームをリセット</a>
          </div>
        </div>
      </div>
    </div>

    <div class="mt-4">
      <a href="index.php" class="btn btn-secondary">← XSSモジュールに戻る</a>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../../include/footer.php'; ?>