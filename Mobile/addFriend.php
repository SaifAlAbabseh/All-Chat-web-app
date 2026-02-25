<?php
if (isset($_REQUEST) && isset($_REQUEST["check"]) && $_REQUEST["check"] == "fromMobile1090" && isset($_REQUEST["username"]) && isset($_REQUEST["password"]) && isset($_REQUEST["friendUsername"])) {
    $you = $_REQUEST["username"];
    $fusername = $_REQUEST["friendUsername"];
    $password = $_REQUEST["password"];

    require_once("../DB.php");

    $check_query = "SELECT * FROM users WHERE BINARY username=? AND password=?";
    $stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($stmt, "ss", $you, $password);
    mysqli_stmt_execute($stmt);
    $check_result = mysqli_stmt_get_result($stmt);
    if ($check_result) {
        if (mysqli_num_rows($check_result) == 0) {
            echo "Unknown Error";
        } else {
            $query = "SELECT * FROM users WHERE BINARY username=?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "s", $fusername);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if (mysqli_num_rows($result)) {
                    $query2 = "SELECT * FROM friends WHERE BINARY user1=? AND BINARY user2=? OR BINARY user1=? AND BINARY user2=?";
                    $stmt2 = mysqli_prepare($conn, $query2);
                    mysqli_stmt_bind_param($stmt2, "ssss", $you, $fusername, $fusername, $you);
                    mysqli_stmt_execute($stmt2);
                    $result2 = mysqli_stmt_get_result($stmt2);
                    if ($result2) {
                        if (mysqli_num_rows($result2)) {
                            echo "Already a friend";
                        } else {
                            $query3 = "INSERT INTO friends VALUES (?,?)";
                            $stmt3 = mysqli_prepare($conn, $query3);
                            mysqli_stmt_bind_param($stmt3, "ss", $you, $fusername);
                            if (mysqli_stmt_execute($stmt3)) {
                                $tablename = "" . $you . "" . $fusername;
                                $query4 = "CREATE TABLE " . $tablename . " (fromwho varchar(1000) , message varchar(1000),pos varchar(1000))";
                                $stmt4 = mysqli_prepare($conn, $query4);
                                if ($stmt4 && mysqli_stmt_execute($stmt4)) {
                                    echo "ok";
                                } else {
                                    echo "Unknown Error";
                                }
                            } else {
                                echo "Unknown Error";
                            }
                        }
                    } else {
                        echo "Unknown Error";
                    }
                } else {
                    echo "Username not found";
                }
        }
    } else {
        echo "Unknown Error";
    }

    mysqli_close($conn);
} else {
    echo "Unknown Error";
}
