<?php
   
    require('authenticate.php');
    require ('connect.php');
    if (!isset($_SESSION['admin'])) {
        echo '<p>Access Denied. Redirecting to home page...</p>';
        echo '<script>setTimeout(function(){ window.location.href = "index.php"; }, 2000);</script>';
        exit;
    }
    
  // Function to fetch all user data and return it as an array for displaying in the forms
  function fetchUsers($conn) {
    // You have already written the query, so we will not duplicate it here.
    $query = "SELECT user_id, username, email FROM users"; // Simplified to match the function's output
    $result = $conn->query($query);
    if ($result && $result->num_rows > 0) {
        // Fetch all results and return them
        return $result->fetch_all(MYSQLI_ASSOC);
    } else {
        // Return an empty array if the query fails or no results
        return [];
    }
}   

// Call the function and store the result in $users
$users = fetchUsers($conn);
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
        
        <!-- Keep the image URL input if you still want users to have the option to link an image -->
        <label for="image_url">Image URL (optional):</label>
        <input type="text" id="image_url" name="image_url">

        <!-- Add a file input field for users to upload an image from their computer -->
        <label for="cat_image">Upload Image (optional):</label>
        <input type="file" id="cat_image" name="cat_image">

        <input type="submit" value="Add Cat">
    </form>
</section>
<!-- New section for displaying users -->
<div class="section" id="user-list">
    <h2>Registered Users</h2>
    <?php 
    $userResult = fetchUsers($conn);
    if ($userResult): ?>
        <table>
            <tr><th>User ID</th><th>Username</th><th>Email</th></tr>
            <?php foreach ($userResult as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['user_id']); ?></td>
                    <td><?= htmlspecialchars($user['username']); ?></td>
                    <td><?= htmlspecialchars($user['email']); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No registered users.</p>
    <?php endif; ?>
</div>


    <h2>User Management</h2>
     <form method="post" action="user_management.php">
        <h3>Create User</h3>
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <label for="email">Email:</label>
        <input type="text" id="email" name="email" required>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <input type="submit" name="create_user" value="Create User">

    </form>

    <section>
        <h2>Update User Details</h2>
        <form method="post" action="user_management.php">
            <label for="userSelect">Select User:</label>
            <select id="userSelect" name="user_id" onchange="document.getElementById('edit_user_id').value=this.value">
                <option value="">Select a user...</option>
                <?php foreach ($users as $user): ?>
                    <option value="<?php echo $user['user_id']; ?>">
                        <?php echo htmlspecialchars($user['username']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <div id="userDetails">
                <input type="hidden" id="edit_user_id" name="user_id">

                <label for="edit_username">Username:</label>
                <input type="text" id="edit_username" name="username" required>

                <label for="edit_email">Email:</label>
                <input type="email" id="edit_email" name="email" required>
                
                <label for="edit_password">Password (new password, leave blank to keep existing):</label>
                <input type="password" id="edit_password" name="password">

                <input type="submit" name="update_user" value="Update User">
            </div>
        </form>
    </section>

    <!-- Delete User Section -->
    <h3>Delete User</h3>
    <form method="post" action="user_management.php">
    <select id="deleteUserSelect" name="user_id">
        <option value="">Select a user...</option>
        <?php foreach ($users as $user): ?>
            <option value="<?php echo $user['user_id']; ?>"><?php echo htmlspecialchars($user['username']); ?></option>
        <?php endforeach; ?>
    </select>
    <input type="button" id="deleteButton" value="Delete">
    </form>
    </section>
    
</body>
</html>
