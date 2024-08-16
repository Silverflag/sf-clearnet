<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once($_SERVER['DOCUMENT_ROOT'] . '/chat/db.php');

// Create necessary tables
$db = getDB();

// Create messages table if not exists
$sqlMessages = "CREATE TABLE IF NOT EXISTS messages (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($db->query($sqlMessages) === TRUE) {
    echo "Messages table created successfully.<br>";
} else {
    echo "Error creating messages table: " . $db->error . "<br>";
}

// Create users table if not exists
$sqlUsers = "CREATE TABLE IF NOT EXISTS users (
    user_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL
)";

if ($db->query($sqlUsers) === TRUE) {
    echo "Users table created successfully.<br>";
} else {
    echo "Error creating users table: " . $db->error . "<br>";
}

$db->close();
?>
