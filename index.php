<?php
session_start();
require 'connect.php'; 


$catBreeds = [];

// SQL query to fetch all data from the cat breed table
$sql = "SELECT `Cat-ID`, `Name`, `Characteristics`, `Care_Instructions`, `Image_URL` FROM catbreed"; 
$result = $conn->query($sql);

// Check if there are any results
if ($result && $result->num_rows > 0) {
    // Fetch all rows and add them to the array
    while($row = $result->fetch_assoc()) {
        $catBreeds[] = $row;
    }
} else {
    $noBreedsMessage = "No cat breeds found.";
}

// Don't forget to close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cat Explorer CMS</title>
    <link rel="stylesheet" href="style.css">

</head>
<body>
    <header>
        <h1>Welcome to Cat Explorer </h1>
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

    <main>
        <section id="search">
            <form action="search.php" method="get">
                <input type="text" name="query" placeholder="Search cat breeds...">
                <button type="submit">Search</button>
            </form>
        </section>

        
        <section id="featured-cats" class="featured-cat">
           
            
            <!-- Loop through the cat breeds and display them -->
            <?php foreach ($catBreeds as $breed): ?>
                <div class="cat-breed-item">
                    <?php if (!empty($breed['Image_URL'])): ?>
                        <img src="<?= htmlspecialchars($breed['Image_URL']) ?>" alt="<?= htmlspecialchars($breed['Name']) ?>">
                    <?php endif; ?>
                    <h3><?= htmlspecialchars($breed['Name']) ?></h3>
                    <a href="breed_detail.php?Cat-ID=<?= urlencode($breed['Cat-ID']) ?>">Learn more</a>
                </div>
            <?php endforeach; ?>
            
            <?php if (empty($catBreeds)): ?>
                <p><?= $noBreedsMessage ?></p>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Cat Explorer CMS. All rights reserved.</p>
    </footer>
</body>
</html>
