<?php
session_start();
require 'connect.php';

define('ADMIN_LOGIN', 'wally');
define('ADMIN_PASSWORD', 'mypass');

if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])
    || ($_SERVER['PHP_AUTH_USER'] != ADMIN_LOGIN)
    || ($_SERVER['PHP_AUTH_PW'] != ADMIN_PASSWORD)) {
    header('HTTP/1.1 401 Unauthorized');
    header('WWW-Authenticate: Basic realm="Cat CMS"');
    exit("Access Denied: Username and password required.");
}

// Your edit or delete logic here...

?>
