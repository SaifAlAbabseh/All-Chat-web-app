<?php
if (isset($_REQUEST) && isset($_REQUEST["check"]) && $_REQUEST["check"] == "fromMobile1090" && isset($_REQUEST["username"]) && isset($_REQUEST["password"]) && isset($_REQUEST["which"])) {
    $username = $_REQUEST["username"];
    $password = $_REQUEST["password"];
    $which = $_REQUEST["which"];

    require_once("../DB.php");

    $query = "SELECT * FROM users WHERE BINARY username=? AND password=?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ss", $username, $password);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (mysqli_num_rows($result) == 0) {
        echo "Unknown Error";
    } else {
        if ($which == 1) {
            $query = "UPDATE users SET available='0' WHERE BINARY username=?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "s", $username);
            if (!mysqli_stmt_execute($stmt)) {
                echo "Unknown Error";
            } else {
                echo "ok";
            }
        } else if ($which == 2) {
            $query = "UPDATE users SET available='1' WHERE BINARY username=?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "s", $username);
            if (!mysqli_stmt_execute($stmt)) {
                echo "Unknown Error";
            } else {
                echo "ok";
            }
        }
    }
}
mysqli_close($conn);
