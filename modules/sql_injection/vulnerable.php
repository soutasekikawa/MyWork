<?php
require_once("../../include/db_connect.php");
$conn = connect_db();

$username = $_GET['username']; // ← バリデーションなし！
$query = "SELECT * FROM users WHERE username = '$username'";
$result = $conn->query($query);

echo "<h2>検索結果：</h2>";
while($row = $result->fetch_assoc()) {
  echo "ユーザー名: " . htmlspecialchars($row['username']) . "<br>";
}
echo "<hr><small>このページは意図的に脆弱です。</small>";
?>
