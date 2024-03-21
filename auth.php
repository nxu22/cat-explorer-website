<?php
session_start();

if (!isset($_SESSION['logged_in']) || !$_SESSION['is_admin']) {
    header('Location: login.php');
    exit;
}
?>
