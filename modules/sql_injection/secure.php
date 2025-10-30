<?php
require_once("../../include/db_connect.php");
$conn = connect_db();

$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $_GET['username']);
$stmt->execute();
$result = $stmt->get_result();

echo "<h2>検索結果：</h2>";
while($row = $result->fetch_assoc()) {
  echo "ユーザー名: " . htmlspecialchars($row['username']) . "<br>";
}
echo "<hr><small>このページは安全なコード例です。</small>";
?>
