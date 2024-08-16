<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('db.php');

// Validate if necessary tables are created
validateTables();

$db = getDB();
$result = $db->query("SELECT * FROM (SELECT * FROM messages ORDER BY id DESC LIMIT 10000) sub ORDER BY id ASC");

if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    echo "<div><strong>" . $row['username'] . ":</strong> " . $row['message'] . "</div>";
  }
} else {
  echo "No messages yet.";
}

$result->free();
?>
