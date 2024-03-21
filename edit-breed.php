<?php
session_start();
require 'connect.php';
require 'authenticate.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_GET['Cat-ID'])) {
    // Sanitize input
    $catID = $_GET['Cat-ID'];
    $name = $_POST['name'];
    $characteristics = $_POST['characteristics'];
    $care_instructions = $_POST['care_instructions'];
    $image_url = $_POST['image_url'];

    // Update database
    $stmt = $conn->prepare("UPDATE catbreed SET `Name` = ?, `Characteristics` = ?, `Care_Instructions` = ?, `Image_URL` = ? WHERE `Cat-ID` = ?");
    $stmt->bind_param("ssssi", $name, $characteristics, $care_instructions, $image_url, $catID);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "<p>Breed updated successfully.</p>";
    } else {
        echo "<p>Failed to update breed. Please check your inputs.</p>";
    }
    $stmt->close();
    
}
if (!isset($_GET['Cat-ID'])) {
    exit('No Cat-ID specified.');
}

$catID = $_GET['Cat-ID'];
$stmt = $conn->prepare("SELECT `Cat-ID`, `Name`, `Characteristics`, `Care_Instructions`, `Image_URL` FROM catbreed WHERE `Cat-ID` = ?");
$stmt->bind_param("i", $catID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    exit('Cat breed not found.');
}

$breedDetails = $result->fetch_assoc();
$stmt->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Breed - <?= htmlspecialchars($breedDetails['Name']) ?></title>
</head>
<body>
    <h1>Edit Cat Breed Details</h1>
    <form action="edit-breed.php?Cat-ID=<?= $catID ?>" method="post">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($breedDetails['Name']) ?>" required><br>

        <label for="characteristics">Characteristics:</label>
        <textarea id="characteristics" name="characteristics" required><?= htmlspecialchars($breedDetails['Characteristics']) ?></textarea><br>

        <label for="care_instructions">Care Instructions:</label>
        <textarea id="care_instructions" name="care_instructions" required><?= htmlspecialchars($breedDetails['Care_Instructions']) ?></textarea><br>

        <label for="image_url">Image URL:</label>
        <input type="text" id="image_url" name="image_url" value="<?= htmlspecialchars($breedDetails['Image_URL']) ?>" required><br>

        <input type="submit" value="Update Breed">
    </form>
</body>
</html>
