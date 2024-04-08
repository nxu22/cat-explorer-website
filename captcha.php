<?php
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Generate a random string of characters
$captchaText = substr(md5(rand()), 0, 6);
$_SESSION['captcha'] = $captchaText;

header('Content-type: image/png');

$image = imagecreatetruecolor(120, 30);
$white = imagecolorallocate($image, 255, 255, 255);
$black = imagecolorallocate($image, 0, 0, 0);
imagefilledrectangle($image, 0, 0, 120, 30, $white);

// Replace 'path/to/font.ttf' with your actual font file location
$fontPath = 'fonts/Inter-VariableFont_slnt,wght.ttf';



// Draw the random string in the image
imagettftext($image, 14, 0, 10, 20, $black, $fontPath, $captchaText);

imagepng($image);
imagedestroy($image);
exit();
?>
