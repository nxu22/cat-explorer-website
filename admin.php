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
<form id="updateUserForm">
    <select id="userSelect" onchange="getUserDetails()">
        <option value="">Select a user...</option>
        <?php foreach ($users as $user): ?>
            <option value="<?php echo $user['user_id']; ?>">
                <?php echo htmlspecialchars($user['username']); ?>
            </option>
        <?php endforeach; ?>
    </select>
</form>

<!-- This section will be populated with user details once a user is selected -->
<div id="userDetails" style="display:none;">
    <form method="post" action="user_management.php">
        <input type="hidden" id="edit_user_id" name="user_id">
        
        <label for="edit_username">Username:</label>
        <input type="text" id="edit_username" name="username" required>

        <label for="edit_email">Email:</label>
        <input type="email" id="edit_email" name="email" required>
        
        <label for="edit_password">Password (change to new password):</label>
        <input type="password" id="edit_password" name="password">
        
        <input type="submit" name="update_user" value="Update User">
    </form>
</div>

    <!-- Delete User Section -->
    <h3>Delete User</h3>
    <form id="deleteUserForm" method="post">
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



<script>//create user
function getUserDetails() {
    const userId = document.getElementById('userSelect').value;
    const userDetailsDiv = document.getElementById('userDetails');

    // Hide the details form if the default option is selected
    if (!userId) {
        userDetailsDiv.style.display = 'none';
        return;
    }

    // Using URLSearchParams to encode the data for the POST request
    const formData = new URLSearchParams();
    formData.append('fetch_user_details', 'true');
    formData.append('user_id', userId);

    fetch('user_management.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not OK');
        }
        return response.json(); // Assuming the response is JSON
    })
    .then(data => {
        // Populate the form fields with the data received
        document.getElementById('edit_username').value = data.username;
        document.getElementById('edit_email').value = data.email;
        // Set the user ID to a hidden field in the form
        document.getElementById('edit_user_id').value = userId;
        // Show the user details form
        userDetailsDiv.style.display = 'block';
    })
    .catch(error => {
        // Handle error here, e.g., user not found or server error
        userDetailsDiv.style.display = 'none';
        console.error('Error:', error);
    });
}

</script>





<script>//delete user
document.getElementById('deleteButton').addEventListener('click', function() {
    var select = document.getElementById('deleteUserSelect');
    var userId = select.value;
    if (userId && confirm('Are you sure you want to delete this user?')) {
        fetch('user_management.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'delete_user=true&user_id=' + encodeURIComponent(userId)
        })
        .then(response => {
            if (response.ok) {
                return response.text();
            } else {
                throw new Error('Server returned an error');
            }
        })
        .then(text => {
            alert('User deleted successfully.');
            // Optionally, remove the user from the select list
            select.querySelector(`option[value="${userId}"]`).remove();
            // Other UI updates or redirect logic here
        })
        .catch(error => {
            alert('There was an error deleting the user.');
            console.error('Error:', error);
        });
    }
});

</script>

</html>
