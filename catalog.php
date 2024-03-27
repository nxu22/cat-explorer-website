<?php
session_start();
require 'connect.php';

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

<!-- Category management form for creating a new category -->
<form id="categoryManagementForm" method="post">
  <label for="category_name">Category Name:</label>
  <input type="text" id="category_name" name="category_name" required>
  <input type="submit" value="Create Category">
</form>

<!-- Category management form for updating an existing category -->
<form id="categoryManagementForm" method="post">
      <!-- Dropdown to select category to edit -->
      <label for="edit_category_id">Edit Category:</label>
      <select id="edit_category_id" name="cat_id">
        <option value="">Select a Category to Edit</option>
        <?php foreach ($categories as $id => $name): ?>
          <option value="<?php echo $id; ?>"><?php echo $name; ?></option>
        <?php endforeach; ?>
      </select>

      <!-- Input field to update the category name -->
      <label for="category_name">Category Name:</label>
      <input type="text" id="category_name" name="category_name" required>

      <input type="submit" value="Update Category">
    </form>

    <script>
    // JavaScript to load the category name into the input field when a category is selected
    document.getElementById('edit_category_id').addEventListener('change', function() {
        var catId = this.value;
        document.getElementById('category_name').value = catId ? <?php echo json_encode($categories); ?>[catId] : '';
    });
    </script>

</body>
</html>
