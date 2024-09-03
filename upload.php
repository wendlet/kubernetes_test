<?php
// Define the target directory for the uploads
$target_dir = "uploads/";

// Ensure the target directory exists
if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true);
}

// Function to handle the upload process
function uploadFile($file, $target_dir, $flag) {
    if ($flag == 1){
        $target_file = $target_dir . "image.jpg";
    }
    else{
        $target_file = $target_dir . "template.jpg";
    }
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
        uploadFile($_FILES["image"], $target_dir, 1);
    } else {
        echo "No image file uploaded or there was an error.<br>";
    }

    if (isset($_FILES["template"]) && $_FILES["template"]["error"] == 0) {
        uploadFile($_FILES["template"], $target_dir, 0);
    } else {
        echo "No template file uploaded or there was an error.<br>";
    }
    $templatePath = 'uploads/template.jpg';
    $imagePath = 'uploads/image.jpg';

    // The URL of the server you want to send the images to
    $url = 'http://127.0.0.1:5000/upload';

    // Check if the image files exist
    if (!file_exists($templatePath) || !file_exists($imagePath)) {
        die('One or both image files not found.');
    }
   

    // Initialize a cURL session
  
    $ch = curl_init();

    if($errno = curl_errno($ch)) {
        $error_message = curl_strerror($errno);
        echo "cURL error ({$errno}):\n {$error_message}";
    }
   
    // Set the URL for the cURL request
    curl_setopt($ch, CURLOPT_URL, $url);

    // Tell cURL that we want to send a POST request
    curl_setopt($ch, CURLOPT_POST, 1);

    // Attach the image files to the POST fields
    $file1 = new CURLFile($templatePath, mime_content_type($templatePath), basename($templatePath));
    $file2 = new CURLFile($imagePath, mime_content_type($imagePath), basename($imagePath));
    $postFields = array('file1' => $file1, 'file2' => $file2);

    // Set the POST fields for the request
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

    // Set options to follow any "Location:" header sent by the server
    #curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

    // Optionally, you can disable SSL verification (not recommended in production)
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

    // Execute the cURL request
    $response = curl_exec($ch);
    #echo $response;

    // Check for errors
    if ($response === false) {
        echo 'cURL error: ' . curl_error($ch);
    } else {
    // Print the server response
        echo 'Server response: ' . $response;
    }

    // Close the cURL session
    curl_close($ch);
        
    // The URL of the image you want to download
    $imageUrl = 'http://127.0.0.1:5000/upload';

    // The path where you want to save the image
    $saveTo = 'results/result.jpg';

    // Use file_get_contents to download the image
    $imageData = file_get_contents($imageUrl);

    if ($imageData === false) {
        die('Error: Could not download the image.');
    }

    // Save the image to the specified path
    if (file_put_contents($saveTo, $imageData) === false) {
        die('Error: Could not save the image.');
    }

    echo "Image downloaded and saved to: " . $saveTo;

//


    $resultPath = 'results/result.jpg';
    if (file_exists($resultPath)) {
        // Display the image on the web page
        echo "<h2>Generated Image:</h2>";
        echo "<img src='$resultPath' alt='Generated Image' style='max-width:300px;'>";
    } else {
        echo "\nFailed to generate the image.";
    }

}

?>
