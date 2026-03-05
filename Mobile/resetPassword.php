<?php
if (isset($_REQUEST) && isset($_REQUEST["check"]) && $_REQUEST["check"] == "fromMobile1090" && isset($_REQUEST["username"]) && isset($_REQUEST["password"]) && isset($_REQUEST["newPassword"])) {
    $you = $_REQUEST["username"];
    $fusername = $_REQUEST["friendUsername"];
    $password = $_REQUEST["password"];
    $newPassword = $_REQUEST["newPassword"];

    require_once("../DB.php");

    $check_query = "SELECT * FROM users WHERE BINARY username=? AND password=?";
    $stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($stmt, "ss", $you, $password);
    mysqli_stmt_execute($stmt);
    $check_result = mysqli_stmt_get_result($stmt);
    if (mysqli_num_rows($check_result) == 0) {
        echo "Unknown Error";
    } else {
        $query = "UPDATE users SET password=? WHERE BINARY username=?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ss", $newPassword, $you);
        mysqli_stmt_execute($stmt);
        echo "Password updated successfully";
    }
    mysqli_close($conn);
} else {
    echo "Unknown Error";
}
