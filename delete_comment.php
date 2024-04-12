<?php
session_start();
require 'connect.php';

if (isset($_POST['delete'])) {
    $comment_id = $_POST['comment_id'];

    // Prepare statement to avoid SQL injection
    $stmt = $conn->prepare("DELETE FROM comments WHERE comment_id = ?");
    $stmt->bind_param('i', $comment_id); // 'i' specifies the variable type is integer
    
    if ($stmt->execute()) {
        echo "Comment deleted successfully.";
    } else {
        echo "Error deleting comment.";
    }

    $stmt->close();
}


?>
