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
    $image_url = $_POST['image_url'];
    $age = $_POST['age']; 
    $born_year = $_POST['born_year']; 

    // Create a SQL query to insert cat details.
    $sql = "INSERT INTO cat (name, size, breed, hair_color, image_url, age, born_year) VALUES (?, ?, ?, ?, ?, ?, ?)";


    // Prepare and bind parameters.
    if ($stmt = $conn->prepare($sql)) { 
        $stmt->bind_param("sssssis", $name, $size, $breed, $hair_color, $image_url, $age, $born_year);

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
    echo "Invalid request method.";
}
?>
