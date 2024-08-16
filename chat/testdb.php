<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('db.php');
$db = getDB();
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}
echo "Connected successfully";
$db->close();
?>