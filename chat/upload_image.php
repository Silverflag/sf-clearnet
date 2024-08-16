<?php
require_once('db.php');

// Set upload directory
$uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/';

// Check if the directory exists, if not, create it
if (!file_exists($uploadDir)) {
  mkdir($uploadDir, 0777, true);
}

// Check if files are uploaded
if (isset($_FILES['image'])) {
  $file = $_FILES['image'];
  $fileName = $file['name'];
  $tmpName = $file['tmp_name'];
  $targetFile = $uploadDir . basename($fileName);

  // Move uploaded file to the uploads directory
  if (move_uploaded_file($tmpName, $targetFile)) {
    // File uploaded successfully, store its path in the database
    $db = getDB();
    $imageUrl = '/uploads/' . $fileName;
    $username = $_SESSION['username'];

    $stmt = $db->prepare("INSERT INTO messages (username, message) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $imageUrl);
    if ($stmt->execute()) {
      echo $imageUrl; // Return the image URL
    } else {
      echo "Error storing image in database";
    }
  } else {
    echo "Error uploading image";
  }
} else {
  echo "No image uploaded";
}
?>
