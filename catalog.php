<?php

session_start();
require 'connect.php';
// Example for Create operation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Assume proper sanitization and validation are done here
    $name = $_POST['name'];
    $characteristics = $_POST['characteristics'];
    $careInstructions = $_POST['care_instructions'];
    
    // Database connection should be established here
    // Assume $conn is your database connection

    if (!empty($_POST['cat_id'])) {
        // Update operation
        $catId = $_POST['cat_id'];
        // Prepare an update statement
        $stmt = $conn->prepare("UPDATE categories SET Name = ?, Characteristics = ?, Care_Instructions = ? WHERE `Cat-ID` = ?");
        $stmt->bind_param("sssi", $name, $characteristics, $careInstructions, $catId);
        $stmt->execute();
    } else {
        // Create operation
        // Prepare an insert statement
        $stmt = $conn->prepare("INSERT INTO categories (Name, Characteristics, Care_Instructions) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $characteristics, $careInstructions);
        $stmt->execute();
    }
    
    // Close statement
    $stmt->close();
    
    // Redirect or display a success message
}

// Close connection
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cat Category Form</title>
    <link rel="stylesheet" href="style.css">

</head>

<body>
<header>
        <h1>Cat Category Form</h1>
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
  <form action="" method="post" id="catForm">
    <!-- If you are updating an existing category, include a hidden input for the category ID -->
    <!-- This should only be output if updating an existing category -->
    <input type="hidden" name="cat_id" value="<?php echo isset($existingCategoryId) ? $existingCategoryId : ''; ?>">
    Category Name: <input type="text" name="name" required>
    Characteristics: <textarea name="characteristics" required></textarea>
    Care Instructions: <textarea name="care_instructions" required></textarea>
    <input type="submit" value="Submit">
  </form>
  
  <select name="category_id" form="catForm">
    <option value="">Select a Category</option>
    <?php
      // Assuming $categories is an array of categories from the database
      foreach ($categories as $category) {
        echo "<option value='" . htmlspecialchars($category['Cat-ID']) . "'>" . htmlspecialchars($category['Name']) . "</option>";
      }
    ?>
  </select>
</body>
</html>