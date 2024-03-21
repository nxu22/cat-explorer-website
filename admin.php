<?php

    session_start();
    require 'authenticate.php';
    require 'connect.php';

?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<link rel="stylesheet" href="style.css">
<title>Add New Cat Breed</title>
</head>
<body>
  <header>
  <h1>Add New Cat Breed</h1>
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

  
   <form action="add_breed.php" method="post" enctype="multipart/form-data">
        
       
        <label for="name">Breed Name:</label>
        <input type="text" id="name" name="name" required>

        <label for="characteristics">Characteristics:</label>
        <textarea id="characteristics" name="characteristics" required></textarea>
        
        <label for="care_instructions">Care Instructions:</label>
        <textarea id="care_instructions" name="care_instructions" required></textarea>
        
        <label for="image_url">Image URL:</label>
        <input type="text" id="image_url" name="image_url">
        
        <input type="submit" value="Add Breed">
    </form>

</body>
</html>
