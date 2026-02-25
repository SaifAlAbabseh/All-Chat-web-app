<?php

if (isset($_REQUEST) && isset($_REQUEST["check"]) && $_REQUEST["check"] == "fromMobile1090" && isset($_REQUEST["username"]) && isset($_REQUEST["password"]) && isset($_REQUEST["tableName"]) && isset($_REQUEST["message"]) && isset($_REQUEST["lastIndex"])) {
    $tableName = $_REQUEST["tableName"];
    $fromWho = $_REQUEST["username"];
    $message = $_REQUEST["message"];
    $lastIndex = $_REQUEST["lastIndex"];

    $username = $_REQUEST["username"];
    $password = $_REQUEST["password"];

    $encodedMessage = urlencode($message);

    require_once("../DB.php");

    $query = "SELECT * FROM users WHERE BINARY username=? AND password=?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ss", $username, $password);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (mysqli_num_rows($result) == 0) {
        echo "Unknown Error";
    } else {
        $query = "INSERT INTO " . $tableName . "(fromwho, message, pos) VALUES (?,?,?)";
        $stmt = mysqli_prepare($conn, $query);
        $pos = ($lastIndex + 1);
        mysqli_stmt_bind_param($stmt, "ssi", $fromWho, $encodedMessage, $pos);
        if (mysqli_stmt_execute($stmt)) {
            echo "ok";
        } else {
            echo "Unknown Error";
        }
    }
}
mysqli_close($conn);
