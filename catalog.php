<?php
session_start();
require 'connect.php'; 
// Fetch categories for the dropdown
$categories = array();
$categoryQuery = "SELECT `Cat-ID`, `BreedName` FROM categories";
$categoryResult = $conn->query($categoryQuery);
if ($categoryResult) {
    while ($row = $categoryResult->fetch_assoc()) {
        $categories[] = $row;
    }
}

// Initialize an empty array for the cats
$cats = array();

// Check if a breed has been selected and fetch cats
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['breed_id']) && $_POST['breed_id'] != '') {
    $breedId = $_POST['breed_id'];
    $stmt = $conn->prepare("SELECT * FROM cat WHERE breed = (SELECT `BreedName` FROM categories WHERE `Cat-ID` = ?)");
    $stmt->bind_param("i", $breedId);
    $stmt->execute();
    $result = $stmt->get_result();
    $cats = $result->fetch_all(MYSQLI_ASSOC);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cat Breed Catalog</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
<header>
  <h1>Cat Breed Catalog</h1>
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

<!-- Form for selecting a breed category -->
<form id="categorySelectionForm" method="post">
  <label for="breed_selection">Select a Breed Category:</label>
  <select id="breed_selection" name="breed_id" onchange="this.form.submit()">
    <option value="">Select a Breed</option>
    <?php foreach ($categories as $category): ?>
      <option value="<?php echo htmlspecialchars($category['Cat-ID']); ?>" <?php echo isset($_POST['breed_id']) && $_POST['breed_id'] == $category['Cat-ID'] ? 'selected' : ''; ?>>
        <?php echo htmlspecialchars($category['BreedName']); ?>
      </option>
    <?php endforeach; ?>
  </select>
</form>

<section>
  <h2>Cats List</h2>
  <div class="cat-list">
    <?php foreach ($cats as $cat): ?>
      <div class="cat">
        <img src="<?php echo htmlspecialchars($cat['image_url']); ?>" alt="<?php echo htmlspecialchars($cat['name']); ?>">
        <h3><?php echo htmlspecialchars($cat['name']); ?></h3>
        <!-- Include other details you want to show for each cat -->
      </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- Category management form -->
<form id="categoryManagementForm" method="post" action="category_management.php">
  <label for="category_name">Category Name:</label>
  <input type="text" id="category_name" name="category_name" required>

  <!-- Include this hidden input only if you are updating a category -->
  <!-- <input type="hidden" name="cat_id" value="<?php echo $existingCatId; ?>"> -->

  <input type="submit" value="Create/Update Category">
</form>

</body>
</html>
