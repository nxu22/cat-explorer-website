<?php
session_start();

// Destroy the user session
$_SESSION = array(); // Unset all session variables
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

// Redirect to the home page
header("Location: index.php");
exit;
?>
