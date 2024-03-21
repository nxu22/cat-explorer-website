<?php
session_start();
require 'connect.php'; 

// Initialize a variable to store potential error messages
$error_message = '';

// Check if the form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email']) && isset($_POST['password'])) {
    $email = trim(strtolower($_POST['email'])); // Convert email to lowercase
    $password = trim($_POST['password']);

    // Prepare a statement to get the user by email
    if ($stmt = $conn->prepare("SELECT user_id, username, password_hash FROM users WHERE LOWER(email) = ?")) {
        $stmt->bind_param("s", $email);

        if ($stmt->execute()) {
            $stmt->store_result();

            if ($stmt->num_rows == 1) {
                $stmt->bind_result($user_id, $username, $password_hash);
                $stmt->fetch();

                if (password_verify($password, $password_hash)) {
                    header('Location: index.php');
                } else {
                    $error_message = "The password you entered was not valid.";
                }
            } else {
                $error_message = "No account found with that email.";
            }
        } else {
            $error_message = "There was an error with the sign-in process.";
        }
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
<title>Sign In</title>
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
<!-- Display error message if any -->
<?php
    if (!empty($error_message)) {
        echo '<div class="alert alert-danger">' . $error_message . '</div>';
    }
    ?>

<div class="signin-container">
    <h2>Sign In</h2>
    <form action="signin.php" method="post">
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Password (8-20 characters)</label>
            <input type="password" id="password" name="password" required minlength="8" maxlength="20">
        </div>
        <div class="form-group">
            <input type="submit" value="Sign In">
        </div>
    </form>
    <div class="links">
        <a href="#">Forgot password?</a> | <a href="register.php">Create an Account</a>
    </div>
</div>

</body>
</html>
