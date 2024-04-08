<?php
session_start();
require 'connect.php';
require 'authenticate.php';


// Fetch all categories for the dropdown
$categories = [];
$categoryResult = $conn->query("SELECT `Cat-ID`, `BreedName` FROM categories");
if ($categoryResult) {
    while ($row = $categoryResult->fetch_assoc()) {
        $categories[$row['Cat-ID']] = $row['BreedName'];
    }
}

// Check if we're updating an existing category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cat_id'], $_POST['new_category_name'])) {
    $catId = $_POST['cat_id'];
    $newCategoryName = $_POST['new_category_name'];
    
    // Update the category
    $stmt = $conn->prepare("UPDATE categories SET BreedName = ? WHERE `Cat-ID` = ?");
    $stmt->bind_param("si", $newCategoryName, $catId);
    if ($stmt->execute()) {
        // Update successful
        header("Location: catalog.php"); // Redirect to avoid resubmission
        exit();
    } else {
        // Update failed
        echo "Error updating category: " . $conn->error;
    }
}


// Category management logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['category_name'])) {
    if (isset($_POST['cat_id'])) {
        // Update existing category
        $catId = $_POST['cat_id'];
        $categoryName = $_POST['category_name'];
        $updateStmt = $conn->prepare("UPDATE categories SET BreedName = ? WHERE `Cat-ID` = ?");
        $updateStmt->bind_param("si", $categoryName, $catId);
        $updateStmt->execute();
        $updateStmt->close();
    } else {
        // Create new category
        $categoryName = $_POST['category_name'];
        $insertStmt = $conn->prepare("INSERT INTO categories (BreedName) VALUES (?)");
        $insertStmt->bind_param("s", $categoryName);
        $insertStmt->execute();
        $insertStmt->close();
    }
    header("Location: catalog.php");
    exit();
}



// Catalog display logic

$categories = array();
$categoryQuery = "SELECT `Cat-ID`, `BreedName` FROM categories";
$categoryResult = $conn->query($categoryQuery);
if ($categoryResult) {
    while ($row = $categoryResult->fetch_assoc()) {
        $categories[] = $row;
    }
}

$cats = array();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['breed_id']) && $_POST['breed_id'] != '') {
    $breedId = $_POST['breed_id'];
    $stmt = $conn->prepare("SELECT * FROM cat WHERE breed = (SELECT `BreedName` FROM categories WHERE `Cat-ID` = ?)");
    $stmt->bind_param("i", $breedId);
    $stmt->execute();
    $result = $stmt->get_result();
    $cats = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
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
        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
            <li><a href="signout.php">SIGN OUT</a></li>
        <?php else: ?>
            <li><a href="signin.php">SIGN IN</a></li>
        <?php endif; ?>
    </ul>
    </nav>


    <div class="container">
  <div class="left">
    <section>
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


  
  </div>

  <div class="right">

  <section>
<!-- Category management form for creating a new category -->
<form id="categoryManagementForm" method="post">
  <label for="category_name">Category Name:</label>
  <input type="text" id="category_name" name="category_name" required>
  <input type="submit" value="Create Category">
</form>

<!-- Category management form for updating an existing category -->
<form id="categoryUpdateForm" method="post">
    <label for="edit_category_id">Edit Category:</label>
    <select id="edit_category_id" name="cat_id">
        <option value="">Select a Category to Edit</option>
        <?php foreach ($categories as $category): ?>
            <option value="<?php echo htmlspecialchars($category['Cat-ID']); ?>">
                <?php echo htmlspecialchars($category['BreedName']); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label for="new_category_name">New Category Name:</label>
    <input type="text" id="new_category_name" name="new_category_name" required>

    <input type="submit" value="Update Category">
</form>

<script>
// Script to set the value of the category name input when a category is selected
document.getElementById('edit_category_id').addEventListener('change', function() {
    var categories = <?php echo json_encode($categories); ?>;
    var categoryNameInput = document.getElementById('new_category_name');
    categoryNameInput.value = this.value ? categories[this.value] : '';
});
</script>

</section>
</div>
</div>
</body>
</html>
