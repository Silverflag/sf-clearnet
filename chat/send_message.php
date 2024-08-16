<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('db.php');

session_start();

// Validate if necessary tables are created
validateTables();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (isset($_POST['message'])) {
    $message = $_POST['message'];

    // Check if the message contains any image links
    preg_match_all('/<img src="(.*?)"/', $message, $matches);
    foreach ($matches[1] as $image) {
      $message = str_replace('<img src="' . $image . '" alt="Uploaded Image">', $image, $message);
    }

    $db = getDB();
    $username = $_SESSION['username'];
    $stmt = $db->prepare("INSERT INTO messages (username, message) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $message);
    $stmt->execute();
    $stmt->close();

    echo "Message sent successfully!";
  } else {
    echo "Error: Message not received.";
  }
}
?>
