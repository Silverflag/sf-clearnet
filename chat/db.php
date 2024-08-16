<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once($_SERVER['DOCUMENT_ROOT'] . '/chat/.env.php');

function getDB() {
  static $db = null;
  if ($db === null) {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($db->connect_error) {
      die("Connection failed: " . $db->connect_error);
    }
  }
  return $db;
}

// Function to validate if necessary tables are created
function validateTables() {
  $db = getDB();
  $result = $db->query("SHOW TABLES LIKE 'messages'");
  if ($result === false) {
    die("Error: " . $db->error); // Add error message for debugging
  }
  if ($result->num_rows == 0) {
    die("Error: Table 'messages' does not exist.");
  }
}
?>