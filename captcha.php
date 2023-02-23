<?php
session_start();

if (isset($_SESSION) && isset($_SESSION["code"])) {
    $text = $_SESSION["code"];
    $fontFile = "Extra/styles/images/captcha/arial.ttf";
    $fontSize = 20;
    $width = 120;
    $height = 50;
    $image = imagecreatetruecolor($width, $height);
    $textColor = imagecolorallocate($image, 0, 0, 0);
    $backgroundColor = imagecolorallocate($image, 255, 255, 255);
    imagefill($image, 0, 0, $backgroundColor);
    $fontPath = realpath($fontFile);
    imagettftext($image, $fontSize, -10, 0, $fontSize, $textColor, $fontPath, $text);
    $image_path = "Extra/styles/images/captcha/captcha.png";
    imagepng($image, $image_path);

    header('Content-type:image/png');
    readfile($image_path);

} else {
    header("Location:../All_Chat");
}
