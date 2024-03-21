<?php
// Start the session and turn on error reporting
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the database connection
include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize the input values
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);;
    $email = strtolower(trim($_POST['email'])); 

    
    // Hash the password
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
        echo "Error: " . $stmt->error;
    }
    
    // Close statement and connection
    $stmt->close();
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
            <span id="togglePassword" style="cursor: pointer;">👁️</span>
        </div>

        <div class="form-group">
            <input type="submit" value="Create Account">
        </div>
    </form>
</div>
</body>
</html>
<script>
// JavaScript function to toggle password visibility
document.getElementById('togglePassword').addEventListener('click', function (e) {
    const passwordInput = document.getElementById('password');
    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordInput.setAttribute('type', type);
    
    // toggle the eye / eye slash icon
    this.textContent = this.textContent === '👁️' ? '🚫' : '👁️';
});
</script>
