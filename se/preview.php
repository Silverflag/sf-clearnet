<?php
// URL to capture
$url = $_GET['url']; // You can pass the URL as a parameter in the query string

// Fetch HTML content of the webpage
$html = file_get_contents($url);

// Generate a temporary HTML file
$tempHtmlFile = tempnam(sys_get_temp_dir(), 'html');
file_put_contents($tempHtmlFile, $html);

// Output file path for the generated PNG image
$outputImagePath = sys_get_temp_dir() . '/screenshot.png';

// Command to convert HTML to image using wkhtmltoimage
$command = "wkhtmltoimage --format png $tempHtmlFile $outputImagePath";

// Execute the command
exec($command);

// Output the image
header('Content-Type: image/png');
readfile($outputImagePath);

// Clean up temporary files
unlink($tempHtmlFile);
unlink($outputImagePath);
?>
