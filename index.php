<?php
session_start();
require 'connect.php';

$cats = [];

// Valid columns to sort by
$valid_sorts = [
    'name_ASC' => 'name ASC',
    'name_DESC' => 'name DESC',
    'age_ASC' => 'age ASC',
    'age_DESC' => 'age DESC',
    'born_year_ASC' => 'born_year ASC',
    'born_year_DESC' => 'born_year DESC'
];

// Check if a valid sort parameter is provided by the user
if (isset($_GET['sort']) && array_key_exists($_GET['sort'], $valid_sorts)) {
    // Get the corresponding ORDER BY clause from the validated sort parameter
    $order_by_clause = $valid_sorts[$_GET['sort']];
} else {
    // Use a default ORDER BY clause
    $order_by_clause = 'name ASC';
}

// SQL query to fetch all data from the cat table and sort it
$sql = "SELECT `id`, `name`, `size`, `breed`, `hair_color`, `image_url`, `age`, `born_year` FROM cat ORDER BY {$order_by_clause}";
$result = $conn->query($sql);

// Check if there are any results
if ($result && $result->num_rows > 0) {
    // Fetch all rows and add them to the array
    while ($row = $result->fetch_assoc()) {
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
        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
            <li><a href="signout.php">SIGN OUT</a></li>
        <?php else: ?>
            <li><a href="signin.php">SIGN IN</a></li>
        <?php endif; ?>
    </ul>
    </nav>

    <main>
    <section id="search">
        <form action="search.php" method="get">
            <input type="text" name="query" placeholder="Search cats...">
            <button type="submit">Search</button>
        </form>
    </section>
    
    <form action="" method="get">
    <select name="sort" onchange="this.form.submit()">
        <option value="name_ASC" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'name_ASC') ? 'selected' : ''; ?>>Sort by Name Ascending</option>
        <option value="name_DESC" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'name_DESC') ? 'selected' : ''; ?>>Sort by Name Descending</option>
        <option value="age_ASC" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'age_ASC') ? 'selected' : ''; ?>>Sort by Age Ascending</option>
        <option value="age_DESC" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'age_DESC') ? 'selected' : ''; ?>>Sort by Age Descending</option>
        <option value="born_year_ASC" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'born_year_ASC') ? 'selected' : ''; ?>>Sort by Born Year Ascending</option>
        <option value="born_year_DESC" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'born_year_DESC') ? 'selected' : ''; ?>>Sort by Born Year Descending</option>
    </select>
</form>

    


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
