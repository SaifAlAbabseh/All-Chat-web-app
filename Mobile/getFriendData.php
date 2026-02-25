<?php
if (isset($_REQUEST) && isset($_REQUEST["check"]) && $_REQUEST["check"] == "fromMobile1090" && isset($_REQUEST["username"]) && isset($_REQUEST["password"]) && isset($_REQUEST["friendUsername"])) {
    $friendUsername = $_REQUEST["friendUsername"];
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
            $query = "SELECT * FROM friends WHERE BINARY user1=? AND BINARY user2=? OR BINARY user1=? AND BINARY user2=?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "ssss", $username, $friendUsername, $friendUsername, $username);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if (mysqli_num_rows($result)) {
                    $query2 = "SELECT available,picture FROM users WHERE BINARY username=?";
                    $stmt2 = mysqli_prepare($conn, $query2);
                    mysqli_stmt_bind_param($stmt2, "s", $friendUsername);
                    mysqli_stmt_execute($stmt2);
                    $result2 = mysqli_stmt_get_result($stmt2);
                    if ($result2) {
                        if (mysqli_num_rows($result2)) {
                            $row = mysqli_fetch_row($result2);

                            $ava = $row[0];
                            $picName = $row[1];

                            echo "" . $ava . "|" . $picName;
                        }
                    } else {
                        echo "Unknown Error";
                    }
                } else {
                    echo "Not a friend";
                }
        }
    mysqli_close($conn);
} else {
    echo "Unknown Error";
}
