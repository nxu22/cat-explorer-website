<?php
session_start();
require 'connect.php'; 

$cats = [];

// SQL query to fetch all data from the cat table
$sql = "SELECT `id`, `name`, `size`, `breed`, `hair_color`, `image_url` FROM cat"; 
$result = $conn->query($sql);

// Check if there are any results
if ($result && $result->num_rows > 0) {
    // Fetch all rows and add them to the array
    while($row = $result->fetch_assoc()) {
        $cats[] = $row;
    }
} else {
    $noCatsMessage = "No cats found.";
}


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
            <input type="text" name="query" placeholder="Search cats...">
            <button type="submit">Search</button>
        </form>
    </section>
    
    <section id="featured-cats" class="featured-cat">
        <!-- Loop through the cats and display their image, name, and a 'Know Me More' button -->
        <?php if (!empty($cats)): ?>
            <?php foreach ($cats as $cat): ?>
                <div class="cat-item">
                    <?php if (!empty($cat['image_url'])): ?>
                        <img src="<?= htmlspecialchars($cat['image_url']) ?>" alt="<?= htmlspecialchars($cat['name']) ?>" class="cat-image">
                    <?php endif; ?>
                    <h3><?= htmlspecialchars($cat['name']) ?></h3>
                    <!-- 'Know Me More' button -->
                    <a href="cat_detail.php?id=<?= urlencode($cat['id']) ?>" class="know-more-btn">Know Me More</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p><?= $noCatsMessage ?></p>
        <?php endif; ?>
    </section>
    </main>

    <footer>
        <p>&copy; 2024 Cat Explorer CMS. All rights reserved.</p>
    </footer>
</body>
</html>
