<?php
session_start();
require 'connect.php'; // This will set up the $conn variable with the database connection

// Handle create user operation
if (isset($_POST['create_user'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, password_hash, email) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $passwordHash, $email);
    if ($stmt->execute()) {
        $_SESSION['message'] = "New user created successfully";
    } else {
        $_SESSION['error'] = "Error: " . $stmt->error;
    }
    $stmt->close();
    header("Location: admin.php");
    exit;
}

// Handle fetch user details operation
if (isset($_POST['fetch_user_details'])) {
    $userId = $_POST['user_id'];
    $stmt = $conn->prepare("SELECT username, email FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    echo json_encode($result); // Send back the results in JSON format
    exit;
}

// Handle update user operation
if (isset($_POST['update_user'])) {
    $userId = $_POST['user_id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    // Check if a new password has been provided
    $updatePassword = !empty($_POST['password']);
    
    if ($updatePassword) {
        $password = $_POST['password'];
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET username = ?, password_hash = ?, email = ? WHERE user_id = ?");
        $stmt->bind_param("sssi", $username, $passwordHash, $email, $userId);
    } else {
        // If no new password was provided, don't update the password column
        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE user_id = ?");
        $stmt->bind_param("ssi", $username, $email, $userId);
    }
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "User updated successfully";
    } else {
        $_SESSION['error'] = "Error: " . $stmt->error;
    }
    $stmt->close();
    header("Location: admin.php");
    exit;
}


// Handle delete user operation
if (isset($_POST['delete_user']) && isset($_POST['user_id'])) {
    $userId = $_POST['user_id'];
    $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    if ($stmt->execute()) {
        $_SESSION['message'] = "User deleted successfully";
    } else {
        $_SESSION['error'] = "Error: " . $stmt->error;
    }
    $stmt->close();
    header("Location: admin.php");
    exit;
}