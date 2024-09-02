<?php
// Define the target directory for the uploads
$target_dir = "uploads/";

// Ensure the target directory exists
if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true);
}

// Function to handle the upload process
function uploadFile($file, $target_dir) {
    $target_file = $target_dir . basename($file["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if the file is an actual image
    $check = getimagesize($file["tmp_name"]);
    if ($check !== false) {
        echo "File is an image - " . $check["mime"] . ".<br>";
        $uploadOk = 1;
    } else {
        echo "File is not an image.<br>";
        $uploadOk = 0;
    }

    // Check file size (limit set to 5MB)
    if ($file["size"] > 5000000) {
        echo "Sorry, your file is too large: " . htmlspecialchars(basename($file["name"])) . ".<br>";
        $uploadOk = 0;
    }

    // Allow only specific file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif") {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed: " . htmlspecialchars(basename($file["name"])) . ".<br>";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded: " . htmlspecialchars(basename($file["name"])) . ".<br>";
    } else {
        // Try to upload the file
        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            echo "The file " . htmlspecialchars(basename($file["name"])) . " has been uploaded.<br>";
            echo "<img src='" . $target_file . "' alt='Uploaded Image' style='max-width:300px;'><br><br>";
        } else {
            echo "Sorry, there was an error uploading your file: " . htmlspecialchars(basename($file["name"])) . ".<br>";
        }
    }
}

// Handle the uploads for both the image and template files
if (isset($_POST["submit"])) {
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        uploadFile($_FILES["image"], $target_dir);
    } else {
        echo "No image file uploaded or there was an error.<br>";
    }

    if (isset($_FILES["template"]) && $_FILES["template"]["error"] == 0) {
        uploadFile($_FILES["template"], $target_dir);
    } else {
        echo "No template file uploaded or there was an error.<br>";
    }
}

$command = escapeshellcmd('/home/tobi/PycharmProjects/template_matching_console/main.py');

$imagePath = 'results/result.jpg';
if (file_exists($imagePath)) {
    // Display the image on the web page
    echo "<h2>Generated Image:</h2>";
    echo "<img src='$imagePath' alt='Generated Image' style='max-width:300px;'>";
} else {
    echo "Failed to generate the image.";
}

?>
