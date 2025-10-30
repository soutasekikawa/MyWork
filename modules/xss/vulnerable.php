<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $content = $_POST['comment']; // ← エスケープなし
  echo "<h3>あなたの投稿：</h3>";
  echo $content; // 攻撃例：<script>alert('XSS')</script>
}
?>
<form method="POST">
  コメント：<input type="text" name="comment">
  <input type="submit" value="送信">
</form>
