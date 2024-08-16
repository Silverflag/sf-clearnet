<?php
// Set upload directory
$uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/';

// Check if the directory exists, if not, create it
if (!file_exists($uploadDir)) {
  mkdir($uploadDir, 0777, true);
}

// Check if files are uploaded
if (isset($_FILES['files'])) {
  $files = $_FILES['files'];
  $uploadedFiles = [];

  // Loop through each file
  foreach ($files['name'] as $key => $name) {
    $tmpName = $files['tmp_name'][$key];
    $targetFile = $uploadDir . basename($name);

    // Move uploaded file to the uploads directory
    if (move_uploaded_file($tmpName, $targetFile)) {
      $uploadedFiles[] = $targetFile;
    } else {
      echo "Error uploading file: " . $name;
    }
  }

  // Output uploaded file paths
  foreach ($uploadedFiles as $file) {
    echo $file . PHP_EOL;
  }
} else {
  echo "No files uploaded";
}
?>
