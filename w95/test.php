<?php

// Assuming the JSON is stored in a file called 'desktopItems.json' on the desktop
$jsonFilePath = $_SERVER['HOMEPATH'] . 'myitems.json';
$jsonString = file_get_contents($jsonFilePath);

// Decode the JSON string into a PHP array
$desktopItems = json_decode($jsonString, true);

function printItems($items, $indent = '') {
    foreach ($items as $item) {
        // Print the item name with indentation for nested items
        echo $indent . '- ' . $item['name'] . "\n";
        
        // If the item is a folder and has contents, recursively print its contents
        if ($item['type'] === 'folder' && !empty($item['contents'])) {
            printItems($item['contents'], $indent . '    ');
        }
    }
}

// Start printing the structure
printItems($desktopItems);

?>