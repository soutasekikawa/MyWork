<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $content = htmlspecialchars($_POST['comment']);
  echo "<h3>あなたの投稿：</h3>";
  echo $content;
}
?>
<form method="POST">
  コメント：<input type="text" name="comment">
  <input type="submit" value="送信">
</form>
