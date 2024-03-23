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
<title>Add New Cat</title>
</head>
<body>
  <header>
  <h1>Add New Cat</h1>
</header>
  <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="catalog.php">Cat Catalog</a></li>
            <li><a href="favorites.php">Favorites</a></li>
            <li><a href="admin.php">Admin Area</a></li>
            <li><a href="signin.php">SIGN IN</a></li>
        </ul>
    </nav>
  
   <form action="add_cat.php" method="post" enctype="multipart/form-data">
        
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>

        <label for="size">Size:</label>
        <input type="text" id="size" name="size" required>

        <label for="breed">Breed:</label>
        <input type="text" id="breed" name="breed" required>

        <label for="hair_color">Hair Color:</label>
        <input type="text" id="hair_color" name="hair_color" required>

        <label for="image_url">Image URL:</label>
        <input type="text" id="image_url" name="image_url" required>
        
        <input type="submit" value="Add Cat">
    </form>

</body>
</html>
