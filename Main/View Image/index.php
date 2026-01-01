<?php

if (isset($_REQUEST) && isset($_REQUEST["u"])) {
    if (isset($_REQUEST["token"])) {
        if ($_REQUEST["token"] == "")
            die("Not valid token..");
        require_once("../../DB.php");
        $token = $_REQUEST["token"];
        $query = "SELECT * FROM users WHERE view_image_token='" . $token . "'";
        $result = mysqli_query($conn, $query);
        if (!($result && mysqli_num_rows($result) > 0)) {
            mysqli_close($conn);
            die("Not valid token..");
        }
        getImage($conn, $_REQUEST["u"]);
    } else {
        session_start();
        if (isset($_SESSION) && isset($_SESSION["who"])) {
            require_once("../../DB.php");
            getImage($conn, $_REQUEST["u"]);
        } else {
            die("Not authorized..");
        }
    }
} else {
    die("Error..");
}

function getImage($conn, $username) {
    $query = "SELECT picture FROM users WHERE BINARY username='" . $username . "'";
    $result = mysqli_query($conn, $query);
    mysqli_close($conn);
    $temp = "user";
    if ($result) {
        if (mysqli_num_rows($result)) {
            $row = mysqli_fetch_row($result);
            $temp = $row[0];
        }
    }
    $path = "../../Extra/styles/images/users images/" . $temp . ".png";
    header('Content-Type: image/png');
    readfile($path);
}

?>