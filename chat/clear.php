<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/chat/.env.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/chat/db.php');

// Get database connection
$db = getDB();

// Drop all tables
$tables = ['messages', 'users'];

foreach ($tables as $table) {
    $sql = "DROP TABLE IF EXISTS $table";
    
    if ($db->query($sql) === TRUE) {
        echo ucfirst($table) . " dropped successfully<br>";
    } else {
        echo "Error dropping $table: " . $db->error . "<br>";
    }
}

// Close database connection
$db->close();
?>