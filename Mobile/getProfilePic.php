<?php
if (isset($_REQUEST) && isset($_REQUEST["check"]) && $_REQUEST["check"] == "fromMobile1090" && isset($_REQUEST["username"]) && isset($_REQUEST["password"])) {
    $username = $_REQUEST["username"];
    $password = $_REQUEST["password"];

    require_once("../DB.php");

    $query = "SELECT * FROM users WHERE BINARY username='" . $username . "' AND password='" . $password . "'";
    $result = mysqli_query($conn, $query);
    if ($result) {
        if (mysqli_num_rows($result) == 0) {
            echo "Unknown Error";
        } else {
            $query = "SELECT picture FROM users WHERE BINARY username='" . $username . "'";
            $result = mysqli_query($conn, $query);
            if ($result) {
                if (mysqli_num_rows($result)) {
                    $row = mysqli_fetch_row($result);
                    $picName = "" . $row[0];
                    echo "" . $picName;
                } else {
                    echo "Unknown Error";
                }
            } else {
                echo "Unknown Error";
            }
        }
    }
    mysqli_close($conn);
} else {
    echo "Unknown Error";
}
