<?php
session_start();
require 'authenticate.php'; // Ensure this script checks for authentication.
require 'connect.php'; // Your database connection file.

// Check if the form is submitted.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capture form data.
    $name = $_POST['name'];
    $characteristics = $_POST['characteristics'];
    $care_instructions = $_POST['care_instructions'];
    $image_url = $_POST['image_url'];

    // Create a SQL query.
    $sql = "INSERT INTO catbreed (name, characteristics, care_instructions, image_url) VALUES ( ?, ?, ?, ?)";

    // Prepare and bind parameters.
    if ($stmt = $conn->prepare($sql)) { // Ensure $conn is your database connection variable from 'connect.php'
        $stmt->bind_param("ssss", $name, $characteristics, $care_instructions, $image_url);

        // Execute the statement.
        if ($stmt->execute()) {
            echo "New cat breed added successfully.";
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
}
?>
