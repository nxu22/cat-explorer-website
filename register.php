<?php
// Start the session and turn on error reporting
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the database connection
include 'connect.php';

// Initialize a variable for error messages
$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize the input values
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']); // Add this line to get the confirmed password
    $email = strtolower(trim($_POST['email'])); 

    // Check if the two passwords match
    if ($password !== $confirm_password) {
        // If passwords do not match, store an error message
        $error_message = 'The passwords do not match. Please try again.';
    } else {
        // Hash the password if passwords do match
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Prepare an insert statement
        $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $password_hash);
        
        // Execute the query and check for errors
        if ($stmt->execute()) {
            echo "<script>
            alert('Account created successfully. Please sign in.');
            window.location.href='signin.php';
          </script>";
            exit;
        } else {
            $error_message = "Error: " . $stmt->error;
        }
        
        // Close statement and connection
        $stmt->close();
    }
    $conn->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="style.css">
<title>Create Account</title>
</head>
<body>
<header>
  <h1>Welcome To Cat Explorer</h1>
</header>
<nav>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="catalog.php">Cat Breed Catalog</a></li>
        <li><a href="favorites.php">Favorites</a></li>
        <li><a href="admin.php">Admin Area</a></li>
        <li><a href="signin.php">SIGN IN</a></li>
    </ul>
</nav>

<div class="signin-container">
    <h2>Create Account</h2>
    <form action="register.php" method="post">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Password (8-20 characters)</label>
            <input type="password" id="password" name="password" required minlength="8" maxlength="20">
       </div>
       <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" required minlength="8" maxlength="20">
        </div>

        <div class="form-group">
            <input type="submit" value="Create Account">
        </div>
    </form>
</div>
</body>
<script>
// JavaScript function to check password match before submitting the form
document.querySelector('form').addEventListener('submit', function (e) {
    var password = document.getElementById('password').value;
    var confirmPassword = document.getElementById('confirm_password').value;
    
    if (password !== confirmPassword) {
        alert('Passwords do not match. Please try again.');
        e.preventDefault(); // Prevent the form from submitting
        return false;
    }
});
</script>
</html>

