<?php
    session_start();
    require 'authenticate.php';
    require 'connect.php';

    // Fetch all user data into the $users array for displaying in the forms
    $query = "SELECT user_id, username, password_hash, email FROM users";
    $result = $conn->query($query);
    if ($result) {
        $users = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        $users = []; // Set to an empty array if the query fails
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<link rel="stylesheet" href="style.css">
<title>Add New Cat</title>
</head>
<body>
  <header>
  <h1>Add New Cat</h1>
</header>
   <nav>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="catalog.php">Cat Breed Catalog</a></li>
        <li><a href="favorites.php">Favorites</a></li>
        <li><a href="admin.php">Admin Area</a></li>
        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
            <li><a href="signout.php">SIGN OUT</a></li>
        <?php else: ?>
            <li><a href="signin.php">SIGN IN</a></li>
        <?php endif; ?>
    </ul>
    </nav>
  

    <section>
    <h2>Add New Cat</h2>
   <form action="add_cat.php" method="post" enctype="multipart/form-data">
        
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>

        <label for="size">Size:</label>
        <input type="text" id="size" name="size" required>

        <label for="breed">Breed:</label>
        <input type="text" id="breed" name="breed" required>

        <label for="hair_color">Hair Color:</label>
        <input type="text" id="hair_color" name="hair_color" required>

        <label for="age">Age:</label>
        <input type="number" id="age" name="age" required>
   
        <label for="born_year">Born Year:</label>
        <input type="number" id="born_year" name="born_year" min="1900" max="2024" required>

        <label for="image_url">Image URL:</label>
        <input type="text" id="image_url" name="image_url" required>
        
        <input type="submit" value="Add Cat">
    </form>
    </section>
    <section>
    <h2>User Management</h2>
     <form method="post" action="user_management.php">
        <h3>Create User</h3>
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <input type="submit" name="create_user" value="Create User">
    </form>

   <h3>Update User</h3>
     <form method="post" action="user_management.php"> <!-- Points to a script where you handle user editing -->
        <select name="user_id" onchange="this.form.submit()">
        <option value="">Select a user...</option>
        <?php foreach ($users as $user): ?>
            <option value="<?php echo $user['user_id']; ?>"><?php echo htmlspecialchars($user['username']); ?></option>
        <?php endforeach; ?>
       </select>
    </form>

    <!-- Delete User Section -->
    <h3>Delete User</h3>
    <form method="post" action="user_management.php">
    <select name="user_id" onchange="return confirm('Are you sure you want to delete this user?') && this.form.submit();">
        <option value="">Select a user...</option>
        <?php foreach ($users as $user): ?>
            <option value="<?php echo $user['user_id']; ?>"><?php echo htmlspecialchars($user['username']); ?></option>
        <?php endforeach; ?>
    </select>
    <input type="submit" name="delete_user" value="Delete" hidden>
    </form>
    </section>
</body>
</html>
