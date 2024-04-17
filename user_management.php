<?php

require 'connect.php'; // This sets up the $conn variable with the database connection

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
    $userID = $_POST['user_id'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $username = $_POST['username']; // Renamed from $profileName for consistency

    if (empty($password)) {
        $stmt = $conn->prepare("SELECT password_hash FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $userID);
        $stmt->execute();
        $result = $stmt->get_result();
        $existingPassword = $result->fetch_assoc()['password_hash'];
        $hashedPassword = $existingPassword;
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    }

    $stmt = $conn->prepare("UPDATE users SET email = ?, password_hash = ?, username = ? WHERE user_id = ?");
    $stmt->bind_param("sssi", $email, $hashedPassword, $username, $userID);
    if ($stmt->execute()) {
        $_SESSION['message'] = "User updated successfully";
    } else {
        $_SESSION['error'] = "Error updating user: " . $stmt->error;
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
?>
