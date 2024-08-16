<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/chat/db.php');

session_start();

if (isset($_POST['username'])) {
    $newUsername = $_POST['username'];
    
    $db = getDB();
    
    // Update the username in the database
    $stmt = $db->prepare("INSERT INTO users (username) VALUES (?) ON DUPLICATE KEY UPDATE username = VALUES(username)");
    $stmt->bind_param("s", $newUsername);
    
    if ($stmt->execute()) {
        // Update session variable
        $_SESSION['username'] = $newUsername;
        echo 'OK';
    } else {
        echo 'Error updating username: ' . $db->error;
    }
} else {
    echo 'No username provided';
}
?>
