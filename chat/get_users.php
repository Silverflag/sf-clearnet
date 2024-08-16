<?php
// Assuming you have a function to get the list of users from your database
require_once('db.php');

$db = getDB();
$stmt = $db->prepare("SELECT DISTINCT username FROM messages");
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
  echo "<li>" . $row['username'] . "</li>";
}

$stmt->close();
?>
