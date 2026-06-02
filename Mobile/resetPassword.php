<?php
if (isset($_REQUEST) && isset($_REQUEST["check"]) && $_REQUEST["check"] == "fromMobile1090" && isset($_REQUEST["username"]) && isset($_REQUEST["password"]) && isset($_REQUEST["newPassword"])) {
    $you = $_REQUEST["username"];
    $password = $_REQUEST["password"];
    $newPassword = $_REQUEST["newPassword"];

    require_once("../DB.php");

    $check_query = "SELECT password FROM users WHERE BINARY username=?";
    $stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($stmt, "s", $you);
    mysqli_stmt_execute($stmt);
    $check_result = mysqli_stmt_get_result($stmt);
    if (mysqli_num_rows($check_result) == 0) {
        echo "Unknown Error";
    } else {
        $row = mysqli_fetch_assoc($check_result);
        $db_pass = $row['password'];
        if (password_verify($password, $db_pass) || md5($password) === $db_pass || $password === $db_pass) {
            $query = "UPDATE users SET password=? WHERE BINARY username=?";
            $stmt2 = mysqli_prepare($conn, $query);
            // Assuming the app sends the raw newPassword. If it sent MD5 before, now it will store BCrypt of MD5.
            $newPasswordHashed = password_hash($newPassword, PASSWORD_BCRYPT);
            mysqli_stmt_bind_param($stmt2, "ss", $newPasswordHashed, $you);
            mysqli_stmt_execute($stmt2);
            echo "Password updated successfully";
        } else {
            echo "Unknown Error";
        }
    }
    mysqli_close($conn);
} else {
    echo "Unknown Error";
}
