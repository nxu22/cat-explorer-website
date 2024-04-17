<?php
session_start();
require 'connect.php'; 

error_reporting(E_ALL);
ini_set('display_errors', 1);


date_default_timezone_set('America/Winnipeg');
$error_message = '';
$preserved_user_name = '';
$preserved_comment_text = '';

// Check if the cat ID is present in the URL
if (!isset($_GET['id'])) {
    echo "No cat specified.";
    exit;
}

$catID = $_GET['id'];
$catDetails = [];
$comments = [];


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

// Handle comment submission and CAPTCHA validation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment'], $_POST['captcha'])) {
    $user_name = filter_input(INPUT_POST, 'user_name', FILTER_SANITIZE_STRING);
    $comment_text = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_STRING);
    $user_captcha = filter_input(INPUT_POST, 'captcha', FILTER_SANITIZE_STRING);

     
    // Check if the CAPTCHA is correct
    if (isset($_SESSION['captcha']) && $_SESSION['captcha'] == $user_captcha) {
        // CAPTCHA is correct, insert the comment
        $timestamp = date('Y-m-d H:i:s');
        $comment_stmt = $conn->prepare("INSERT INTO comments (cat_id, user_name, comment_text, timestamp) VALUES (?, ?, ?, ?)");
        $comment_stmt->bind_param("isss", $catID, $user_name, $comment_text, $timestamp);
        $comment_stmt->execute();
        $comment_stmt->close();
        
        // Redirect to prevent form resubmission
        header("Location: cat_detail.php?id=$catID");
        exit;
    } else {
        // CAPTCHA is incorrect, set an error message
        $error_message = "CAPTCHA is incorrect. Please try again.";
        
        // Preserving the user input
        $preserved_user_name = $user_name;
        $preserved_comment_text = $comment_text;
    }
    
    // Unset the CAPTCHA session after checking to avoid reuse
    unset($_SESSION['captcha']);
    
    // Store the error message and preserved user input in the session to display after redirect
    $_SESSION['error_message'] = $error_message;
    $_SESSION['preserved_user_name'] = $preserved_user_name;
    $_SESSION['preserved_comment_text'] = $preserved_comment_text;

    // Redirect back to the form with preserved inputs and error message
    header("Location: cat_detail.php?id=$catID");
    exit;
}

// Retrieve any error message or preserved user input after the redirect
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    $preserved_user_name = $_SESSION['preserved_user_name'];
    $preserved_comment_text = $_SESSION['preserved_comment_text'];
    // Clear them from the session
    unset($_SESSION['error_message'], $_SESSION['preserved_user_name'], $_SESSION['preserved_comment_text']);
}

// Comments retrieval
$comments_stmt = $conn->prepare("SELECT user_name, comment_text, timestamp FROM comments WHERE cat_id = ? ORDER BY timestamp DESC");
$comments_stmt->bind_param("i", $catID);
$comments_stmt->execute();
$comments_result = $comments_stmt->get_result();
while ($row = $comments_result->fetch_assoc()) {
    $comments[] = $row;
}

// Place this before any HTML where you need to display comments
$query = "SELECT * FROM comments WHERE cat_id = ?"; // Replace with your conditions
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $catID); // Bind the $catID variable to the parameter
$stmt->execute();
$result = $stmt->get_result();

$comments = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $comments[] = $row;
    }
} else {
    echo "No comments found for this cat.";
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
        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
            <li><a href="signout.php">SIGN OUT</a></li>
        <?php else: ?>
            <li><a href="signin.php">SIGN IN</a></li>
        <?php endif; ?>
    </ul>
    </nav>

    <main>
        <section id="cat-details">
            <?php if (!empty($catDetails)): ?>
                <img src="<?= htmlspecialchars($catDetails['image_url']) ?>" alt="<?= htmlspecialchars($catDetails['name']) ?>" class="cat-detail-image">
                <h2><?= htmlspecialchars($catDetails['name']) ?></h2>
                <p><strong>Size:</strong> <?= htmlspecialchars($catDetails['size']) ?></p>
                <p><strong>Breed:</strong> <?= htmlspecialchars($catDetails['breed']) ?></p>
                <p>Hair Color: <?= $catDetails['hair_color']; ?></p>
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

        <section id="comments">
            <h3>Comments</h3>
            <form action="cat_detail.php?id=<?= htmlspecialchars($catID) ?>" method="post">
            <input type="text" name="user_name" placeholder="Your name" value="<?= htmlspecialchars($preserved_user_name) ?>">
                <textarea name="comment" placeholder="Write a comment..." required><?= htmlspecialchars($preserved_comment_text) ?></textarea>
                <div id="captcha-container">
                    <img src="captcha.php" alt="CAPTCHA Image">
                    <input type="text" name="captcha" placeholder="Enter CAPTCHA" required>
                    <!-- Display an error message if it's set -->
                   <?php if (!empty($error_message)): ?>
                   <div class="notification-box">
                    <?php echo htmlspecialchars($error_message); ?>
                    </div>
                    <?php endif; ?>    
                </div>
                <button type="submit">Post Comment</button>
            </form>
       

            <div class="comments-list">
    <?php foreach ($comments as $comment): ?>
    <article class="comment">
        <header>
            <h4>Name: <?= htmlspecialchars($comment['user_name']) ?></h4>
            <p>Comment: <?= htmlspecialchars($comment['comment_text']) ?></p>
            <time datetime="<?= htmlspecialchars($comment['timestamp']) ?>"><?= htmlspecialchars($comment['timestamp']) ?></time>
        </header>
        <form action="delete_comment.php" method="post">
    <?php if (isset($comment['comment_id'])): ?>
        <input type="hidden" name="comment_id" value="<?php echo htmlspecialchars($comment['comment_id']); ?>">
        <button type="submit" name="delete" value="delete">Delete</button>
    <?php else: ?>
        <!-- Handle the case where $comment['comment_id'] is not set -->
        <!-- This could be an error message or a hidden input with a default value -->
        <p>Error: No comment ID found.</p>
    <?php endif; ?>
</form>

    </article>
    <?php endforeach; ?>
</div>

    </section>
    </main>

    <footer>
        <p>&copy; 2024 Cat Explorer CMS. All rights reserved.</p>
    </footer>

</body>
</html>