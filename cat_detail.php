<?php
session_start();
require 'connect.php'; 

// Check if the cat ID is present in the URL
if (!isset($_GET['id'])) {
    echo "No cat specified.";
    exit;
}

$catID = $_GET['id'];

$catDetails = [];

// SQL query to fetch data for the specific cat
$sql = "SELECT `id`, `name`, `size`, `breed`, `hair_color`, `image_url`, `age`, `born_year` FROM cat WHERE `id` = ?"; 
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $catID); 
$stmt->execute();
$result = $stmt->get_result();

// Check if there are any results
if ($result && $result->num_rows > 0) {
    $catDetails = $result->fetch_assoc(); // Fetch the details of the cat
} else {
    echo "No details found for the specified cat.";
    exit;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($catDetails['name']) ?> - Cat Details</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1><?= htmlspecialchars($catDetails['name']) ?> - Detailed Information</h1>
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
        <section id="cat-details">
            <?php if (!empty($catDetails)): ?>
                <img src="<?= htmlspecialchars($catDetails['image_url']) ?>" alt="<?= htmlspecialchars($catDetails['name']) ?>" class="cat-detail-image">
                <h2><?= htmlspecialchars($catDetails['name']) ?></h2>
                <p><strong>Size:</strong> <?= htmlspecialchars($catDetails['size']) ?></p>
                <p><strong>Breed:</strong> <?= htmlspecialchars($catDetails['breed']) ?></p>
                <p><strong>Hair Color:</strong> <?= htmlspecialchars($catDetails['hair_color']) ?></p>
                <p><strong>Age:</strong> <?= htmlspecialchars($catDetails['age']) ?> years</p>
                <p><strong>Born Year:</strong> <?= htmlspecialchars($catDetails['born_year']) ?></p>
            <?php else: ?>
                <p>No details found for the specified cat.</p>
            <?php endif; ?>
            <div id="admin-options">
              <a href="edit-cat.php?id=<?= $catID ?>" class="edit-btn">Edit</a>
              <a href="delete-cat.php?id=<?= $catID ?>" class="delete-btn">Delete</a>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Cat Explorer CMS. All rights reserved.</p>
    </footer>

</body>
</html>

           
