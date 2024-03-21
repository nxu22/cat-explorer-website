<?php
session_start();
require 'connect.php'; 

// Check if the Cat-ID is present in the URL
if (!isset($_GET['Cat-ID'])) {
    echo "No cat breed specified.";
    exit;
}


$catID = $_GET['Cat-ID'];

$breedDetails = [];

// SQL query to fetch data for the specific cat breed
$sql = "SELECT `Cat-ID`, `Name`, `Characteristics`, `Care_Instructions`, `Image_URL` FROM catbreed WHERE `Cat-ID` = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $catID); // "i" denotes the parameter type is integer
$stmt->execute();
$result = $stmt->get_result();

// Check if there are any results
if ($result && $result->num_rows > 0) {
    $breedDetails = $result->fetch_assoc(); // Fetch the details of the breed
} else {
    echo "No details found for the specified cat breed.";
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
  <title><?= htmlspecialchars($breedDetails['Name']) ?> - Cat Breed Details</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1><?= htmlspecialchars($breedDetails['Name']) ?> - Detailed Information</h1>
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
        <section id="breed-details">
            <?php if (!empty($breedDetails)): ?>
                <img src="<?= htmlspecialchars($breedDetails['Image_URL']) ?>" alt="<?= htmlspecialchars($breedDetails['Name']) ?>">
                <h2><?= htmlspecialchars($breedDetails['Name']) ?></h2>
                <h3>Characteristics:</h3>
                <p><?= htmlspecialchars($breedDetails['Characteristics']) ?></p>
                <h3>Care Instructions:</h3>
                <p><?= htmlspecialchars($breedDetails['Care_Instructions']) ?></p>
            <?php else: ?>
                <p>No details found for the specified cat breed.</p>
            <?php endif; ?>
            <!-- Edit/Delete Options Placeholder -->
           <div id="admin-options">
              <a href="edit-breed.php?Cat-ID=<?= $catID ?>" class="edit-btn">Edit</a>
              <a href="delete-breed.php?Cat-ID=<?= $catID ?>" class="delete-btn">Delete</a>
            </div>

        </section>
    </main>

    <footer>
        <p>&copy; 2024 Cat Explorer CMS. All rights reserved.</p>
    </footer>

</body>
</html>
