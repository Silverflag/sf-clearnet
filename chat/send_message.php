<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('db.php');

session_start();

validateTables();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (isset($_POST['message'])) {
    $message = $_POST['message'];

    if (strlen($message) > 512) {
      $message = "Message exceeds the length limit, which is set to 512 characters. It has been edited.";
    }

    preg_match_all('/<img src="(.*?)"/', $message, $matches);
    foreach ($matches[1] as $image) {
      $message = str_replace('<img src="' . $image . '" alt="Uploaded Image">', $image, $message);
    }

    $db = getDB();
    $username = $_SESSION['username'];
    if (strlen($username) > 14) {
      $username = "toolong";
    }
    $usernameWithTime = $username . ' <small style="font-size: smaller;">(' . date('M j') . ' <small style="font-size: x-small;">' . date('H:i:s') . '</small>)</small>';

    $stmt = $db->prepare("INSERT INTO messages (username, message) VALUES (?, ?)");
    $stmt->bind_param("ss", $usernameWithTime, $message);
    $stmt->execute();
    $stmt->close();

    echo "Message sent successfully!";
  } else {
    echo "Error: Message not received.";
  }
}
?>
