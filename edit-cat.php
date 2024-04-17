<?php
session_start();
require 'connect.php';
require 'authenticate.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_GET['id'])) {
    // Sanitize input
    $catID = $_GET['id'];
    $name = $_POST['name'];
    $size = $_POST['size'];
    $breed = $_POST['breed'];
    $hair_color = $_POST['hair_color']; // This is now coming from CKEditor
    $image_url = $_POST['image_url'];

    // Assume that $hair_color is sanitized appropriately before storing to the database
    // Update database
    $stmt = $conn->prepare("UPDATE cat SET name = ?, size = ?, breed = ?, hair_color = ?, image_url = ? WHERE id = ?");
    $stmt->bind_param("sssssi", $name, $size, $breed, $hair_color, $image_url, $catID);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo 'Cat ID is: ' . $catID;
        header('Location: /wd2/Project/cat-explorer-website/cat_detail.php?id=' . $catID);

        exit(); 
    } else {
        echo "<p>Failed to update cat. Please check your inputs.</p>";
    }
    $stmt->close();
}

if (!isset($_GET['id'])) {
    exit('No cat ID specified.');
}

$catID = $_GET['id'];
$stmt = $conn->prepare("SELECT id, name, size, breed, hair_color, image_url FROM cat WHERE id = ?");
$stmt->bind_param("i", $catID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    exit('Cat not found.');
}

$catDetails = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Cat - <?= htmlspecialchars($catDetails['name']) ?></title>
    <script src="https://cdn.ckeditor.com/4.16.0/standard/ckeditor.js"></script>
</head>
<body>
    <h1>Edit Cat Details</h1>
    <form action="edit-cat.php?id=<?= $catID ?>" method="post">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($catDetails['name']) ?>" required><br>

        <label for="size">Size:</label>
        <input type="text" id="size" name="size" value="<?= htmlspecialchars($catDetails['size']) ?>" required><br>

        <label for="breed">Breed:</label>
        <input type="text" id="breed" name="breed" value="<?= htmlspecialchars($catDetails['breed']) ?>" required><br>

        <label for="hair_color">Hair Color:</label>
        <textarea id="hair_color" name="hair_color" required><?= $catDetails['hair_color'] // Here we don't escape because it's HTML ?></textarea><br>

        <label for="image_url">Image URL:</label>
        <input type="text" id="image_url" name="image_url" value="<?= htmlspecialchars($catDetails['image_url']) ?>" required><br>

        <input type="submit" value="Update Cat">
    </form>

    <script>
        CKEDITOR.replace('hair_color');
    </script>
</body>
</html>
