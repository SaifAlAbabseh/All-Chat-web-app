<?php
if (isset($_REQUEST) && isset($_REQUEST["check"]) && $_REQUEST["check"] == "fromMobile1090" && isset($_REQUEST["username"]) && isset($_REQUEST["password"])) {
    $username = $_REQUEST["username"];
    $password = $_REQUEST["password"];

    require_once("../DB.php");

    $query = "SELECT * FROM users WHERE BINARY username=? AND password=?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ss", $username, $password);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (mysqli_num_rows($result) == 0) {
        echo "Unknown Error";
    } else {
        $query = "SELECT * FROM friends WHERE BINARY user1=? OR BINARY user2=?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ss", $username, $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($result)) {
            $after = "";
            while ($row = mysqli_fetch_row($result)) {
                $user1 = $row[0];
                $user2 = $row[1];
                $get = "SELECT * FROM users WHERE BINARY username=?";
                $stmt2 = mysqli_prepare($conn, $get);
                if ($user1 == $username) {
                    $temp = 1;
                    mysqli_stmt_bind_param($stmt2, "s", $user2);
                } else if ($user2 == $username) {
                    $temp = 2;
                    mysqli_stmt_bind_param($stmt2, "s", $user1);
                }
                mysqli_stmt_execute($stmt2);
                $result2 = mysqli_stmt_get_result($stmt2);
                if (mysqli_num_rows($result2)) {
                    while ($row2 = mysqli_fetch_row($result2)) {
                        $ava;
                        if ($row2[3] == "0") {
                            $ava = "0";
                        } else if ($row2[3] == "1") {
                            $ava = "1";
                        }
                        $after .= "" . $row2[0] . "|" . $row2[2] . "|" . $row2[3] . "|" . $temp;
                    }
                    $after .= "&";
                } else {
                    echo "Unknown Error";
                }
            }
            echo $after;
        } else {
            echo "No Friends";
        }
    }
    mysqli_close($conn);
} else {
    echo "Unknown Error";
}
