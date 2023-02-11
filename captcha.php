<?php
session_start();

if(isset($_SESSION) && isset($_SESSION["code"]) && isset($_REQUEST) && isset($_REQUEST["p"])){
    $char = "" . $_SESSION["code"][$_REQUEST["p"] - 1];

    $char_image_path = "Extra/styles/images/Characters/" . $char . ".png";

    header('Content-type:image/png');
    readfile($char_image_path);
}
else {
    header("Location:../All_Chat");
}
?>