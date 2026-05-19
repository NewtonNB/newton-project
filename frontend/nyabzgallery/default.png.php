<?php
// Generate a simple default avatar image
header('Content-Type: image/png');

// Create a 300x300 image
$width = 300;
$height = 300;

// Create blank image
$im = @imagecreatetruecolor($width, $height)
    or die("Cannot Initialize new GD image stream");

// Set colors
$bg = imagecolorallocate($im, 102, 126, 234); // #667eea
$white = imagecolorallocate($im, 255, 255, 255);

// Fill background
imagefilledrectangle($im, 0, 0, $width, $height, $bg);

// Draw a simple person icon
// Head (circle)
imagefilledellipse($im, 150, 100, 80, 80, $white);

// Body (rounded rectangle approximation)
imagefilledarc($im, 150, 200, 160, 160, 0, 180, $white, IMG_ARC_PIE);

// Output
imagepng($im);
imagedestroy($im);
?>
