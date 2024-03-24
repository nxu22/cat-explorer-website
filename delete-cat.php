<?php
session_start();
require 'connect.php';
require 'authenticate.php';

$catID = isset($_GET['id']) ? (int)$_GET['id'] : null; // Get the 'id' from URL

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the form's submit button is clicked and if 'id' is passed correctly.
    if (isset($_POST['submit']) && isset($_POST['id'])) {
        $catID = $_POST['id'];

        // SQL to delete the cat
        $stmt = $conn->prepare("DELETE FROM cat WHERE id = ?");
        $stmt->bind_param("i", $catID);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo "<p>Cat deleted successfully.</p>";
            header("Location: success-page.php"); // Redirect to a success page
            exit;
        } else {
            echo "<p>Could not delete cat. It may not exist or an error occurred.</p>";
        }
        $stmt->close();
    } else {
        echo "<p>No cat ID specified or incorrect request method.</p>";
    }
    $conn->close();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Cat</title>
</head>
<body>
    <h1>Delete Cat</h1>
    <?php if ($catID): ?>
        <form action="delete-cat.php" method="post">
            <input type="hidden" name="id" value="<?= htmlspecialchars($catID) ?>">
            <input type="submit" name="submit" value="Delete Cat" onclick="return confirm('Are you sure you want to delete this cat?');">
        </form>
    <?php else: ?>
        <p>Cat ID is not specified.</p>
    <?php endif; ?>
</body>
</html>
