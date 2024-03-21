<?php
session_start();
require 'connect.php';
require 'authenticate.php';



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $catID = $_GET['Cat-ID'];

    // Perform CSRF check here if implemented

    // SQL to delete the breed
    $stmt = $conn->prepare("DELETE FROM catbreed WHERE `Cat-ID` = ?");
    $stmt->bind_param("i", $catID);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Deletion was successful
        echo "<p>Breed deleted successfully.</p>";
        // Redirect or include logic to display an empty detail page
    } else {
        // Deletion failed
        echo "<p>Could not delete breed. It may not exist or an error occurred.</p>";
    }
    $stmt->close();
    $conn->close();
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<a href="delete-breed.php?Cat-ID=<?= $catID ?>" onclick="return confirm('Are you sure you want to delete this breed?');">Delete</a>
</html>