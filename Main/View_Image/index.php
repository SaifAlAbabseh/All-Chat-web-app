<?php

if (isset($_REQUEST) && isset($_REQUEST["u"]) && $_REQUEST["u"] != "") {
    require_once("../../DB.php");
    require_once("../../common.php");
    $imageName = getUserImageName($conn, $_REQUEST["u"]);
    if($imageName == null)
        die("Error..");
    $imagePath = "../../Extra/styles/images/users_images/" . $imageName . ".png";
    header('Content-Type: image/png');
    readfile($imagePath);
} else {
    die("Error..");
}

?>