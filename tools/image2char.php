<?php
function imageToBraille($imagePath) {
    $image = imagecreatefromstring(file_get_contents($imagePath));
    
    $width = imagesx($image);
    $height = imagesy($image);
    
    $maxWidth = 80;
    $maxHeight = 40;
    if ($width > $maxWidth * 2 || $height > $maxHeight * 4) {
        $ratio = min($maxWidth * 2 / $width, $maxHeight * 4 / $height);
        $newWidth = intval($width * $ratio);
        $newHeight = intval($height * $ratio);
        $resized = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        $image = $resized;
        $width = $newWidth;
        $height = $newHeight;
    }
    
    imagefilter($image, IMG_FILTER_GRAYSCALE);
    
    $brailleBase = 0x2800;
    
    $output = '';
    for ($y = 0; $y < $height; $y += 4) {
        for ($x = 0; $x < $width; $x += 2) {
            $byte = 0;
            for ($dy = 0; $dy < 4 && ($y + $dy) < $height; $dy++) {
                for ($dx = 0; $dx < 2 && ($x + $dx) < $width; $dx++) {
                    $rgb = imagecolorat($image, $x + $dx, $y + $dy);
                    $gray = ($rgb & 0xFF);
                    if ($gray < 128) {
                        $byte |= 1 << (($dy * 2) + $dx);
                    }
                }
            }
            $output .= mb_chr($brailleBase + $byte, 'UTF-8');
        }
        $output .= "\n";
    }
    
    imagedestroy($image);
    return $output;
}

$brailleArt = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES['image']['tmp_name'];
        try {
            $brailleArt = imageToBraille($tmpName);
        } catch (Exception $e) {
            $error = "Error processing image: " . $e->getMessage();
        }
    } else {
        $error = "Error uploading file. Please try again. If it fails again, try another network from a different location.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>sf-tools braille</title>
    <style>
        body {
            background-color: #000;
            color: #fff;
            font-family: 'Courier New', monospace;
            font-weight: bold;
            padding: 20px;
        }
        form {
            margin-bottom: 20px;
        }
        input[type="file"] {
            margin-bottom: 10px;
        }
        input[type="submit"] {
            background-color: #9f9;
            color: #000;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
        }
        #braille-output {
            white-space: pre;
            font-family: monospace;
            line-height: 1;
            overflow-x: auto;
        }
        .error {
            color: #ff6666;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <h1> //Braille Image Utility\\ </h1>
    <form action="" method="post" enctype="multipart/form-data">
        <input type="file" name="image" accept="image/*" required>
        <br>
        <input type="submit" value="Convert">
    </form>

    <?php if ($error): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <?php if ($brailleArt): ?>
        <h2>Braille Art Output</h2>
        <div id="braille-output"><?php echo htmlspecialchars($brailleArt); ?></div>
    <?php endif; ?>
</body>
</html>