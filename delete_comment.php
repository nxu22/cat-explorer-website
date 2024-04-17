<?php
session_start();
require 'connect.php';

if (isset($_POST['delete']) && isset($_POST['comment_id'])) {
    $comment_id = $_POST['comment_id'];
   

    // Prepare statement to avoid SQL injection
    $stmt = $conn->prepare("DELETE FROM comments WHERE comment_id = ?");
    $stmt->bind_param('i', $comment_id); // 'i' specifies the variable type is integer
    
    if ($stmt->execute()) {
        // Set a session message to show after redirection
        $_SESSION['message'] = "Comment deleted successfully.";
    } else {
        // Set an error message to show after redirection
        $_SESSION['message'] = "Error deleting comment.";
    }

    $stmt->close();

   
}
?>
