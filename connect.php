<?php
session_start();
$host = 'localhost'; 
$dbname = 'serverside';
$user = 'serveruser';
$pass = 'gorgonzola7!';

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

 