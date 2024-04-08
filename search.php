<?php
session_start();
require 'connect.php';

// The search term from the user
$searchTerm = isset($_GET['query']) ? $_GET['query'] : '';

// Initialize an array to store the search results
$searchResults = [];

// Error message variable
$errorMessage = null;
$noCatsMessage = null;

// The SQL query with wildcard for a LIKE search, focusing solely on the name
// Adjusted to match names starting with the search term
$sql = "SELECT `id`, `name`, `size`, `breed`, `hair_color`, `image_url`, `age`, `born_year` FROM cat WHERE `name` LIKE ?";

// Prepare the SQL statement
if ($stmt = $conn->prepare($sql)) {
    // Prepare the search term to match names starting with the input
    $searchTermLike = $searchTerm . '%';
    $stmt->bind_param('s', $searchTermLike); // Bind the search term parameter

    // Execute the statement
    if ($stmt->execute()) {
        // Get the result
        $result = $stmt->get_result();

        // Check if there are any results
        if ($result && $result->num_rows > 0) {
            // Fetch all rows and add them to the array
            while ($row = $result->fetch_assoc()) {
                $searchResults[] = $row;
            }
        } else {
            $noCatsMessage = "No cats matching your search were found.";
        }
    } else {
        $errorMessage = "Error executing search query.";
    }

    $stmt->close();
} else {
    $errorMessage = "Error preparing search query.";
}

$conn->close();

if ($errorMessage) {
    // Handle error (e.g., log it, show it to the user, etc.)
    echo "<p>Error: " . htmlspecialchars($errorMessage) . "</p>";
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - Cat Explorer CMS</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Search Results</h1>
    </header>


    <main>
   

    <section id="search-results">
        <!-- Display the search results or the noCatsMessage -->
        <?php if (!empty($searchResults)): ?>
            <?php foreach ($searchResults as $cat): ?>
                <div class="cat-item">
                    <?php if (!empty($cat['image_url'])): ?>
                        <img src="<?= htmlspecialchars($cat['image_url']) ?>" alt="<?= htmlspecialchars($cat['name']) ?>" class="cat-image">
                    <?php endif; ?>
                    <h3><?= htmlspecialchars($cat['name']) ?></h3>
                    <a href="cat_detail.php?id=<?= urlencode($cat['id']) ?>" class="know-more-btn">Know Me More</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p><?= $noCatsMessage ?></p>
        <?php endif; ?>
    </section>
    </main>

    
</body>
</html>

