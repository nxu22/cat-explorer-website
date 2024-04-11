<?php
session_start();
require 'authenticate.php'; 
require 'connect.php'; 

// Check if the form is submitted.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capture form data.
    $name = $_POST['name'];
    $size = $_POST['size'];
    $breed = $_POST['breed'];
    $hair_color = $_POST['hair_color'];
    $age = $_POST['age']; 
    $born_year = $_POST['born_year']; 
    $imagePath = '';  // Default to empty if no image is provided

    // Check if an image file is uploaded.
    if (isset($_FILES['cat_image']) && $_FILES['cat_image']['error'] === UPLOAD_ERR_OK) {
        // Verify MIME type
        $fileMimeType = mime_content_type($_FILES['cat_image']['tmp_name']);
        if (in_array($fileMimeType, ['image/jpeg', 'image/png', 'image/gif'])) {
            // Verify the image using getimagesize()
            if ($imgData = getimagesize($_FILES['cat_image']['tmp_name'])) {
                // Generate a unique file name to avoid conflicts
                $uploadDir = 'C:/xampp/htdocs/wd2/Project/cat-explorer-website/uploadimage/';
                $fileExtension = pathinfo($_FILES['cat_image']['name'], PATHINFO_EXTENSION);
                $filenameToStore = uniqid("cat_", true) . '.' . $fileExtension;
                $fullPath = $uploadDir . $filenameToStore;

                // Move the file to the uploads directory
                if (move_uploaded_file($_FILES['cat_image']['tmp_name'], $fullPath)) {
                    // After a successful upload, convert the server path to a URL
                    $imagePath = 'http://localhost/wd2/Project/cat-explorer-website/uploadimage/' . $filenameToStore;
                } else {
                    echo "Failed to upload image.";
                    exit;
                }
            } else {
                echo "Uploaded file is not a valid image.";
                exit;
            }
        } else {
            echo "Invalid image file type.";
            exit;
        }
    } elseif (!empty($_POST['image_url'])) {
        // If an image URL is provided, validate and use it.
        $imagePath = filter_var($_POST['image_url'], FILTER_SANITIZE_URL);
        // You can add additional validation for the URL here
    }

    // Prepare SQL query to insert cat details.
    $sql = "INSERT INTO cat (name, size, breed, hair_color, image_url, age, born_year) VALUES (?, ?, ?, ?, ?, ?, ?)";

    // Prepare and bind parameters.
    if ($stmt = $conn->prepare($sql)) { 
        $stmt->bind_param("sssssis", $name, $size, $breed, $hair_color, $imagePath, $age, $born_year);

        // Execute the statement.
        if ($stmt->execute()) {
            $_SESSION['message'] = "New cat added successfully."; 
            header("Location: admin.php"); 
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }

        // Close statement.
        $stmt->close();
    } else {
        echo "Error: " . $conn->error;
    }

    // Close the database connection.
    $conn->close();
} else {
    // Redirect or display an error if not accessed via a POST request.
    header("Location: add_cat_form.php");
    exit();
}
?>
