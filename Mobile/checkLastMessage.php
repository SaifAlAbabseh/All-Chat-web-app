<?php
if (isset($_REQUEST) && isset($_REQUEST["check"]) && $_REQUEST["check"] == "fromMobile1090" && isset($_REQUEST["username"]) && isset($_REQUEST["password"]) && isset($_REQUEST["tableName"]) && isset($_REQUEST["lastIndex"])) {
    $tablename = $_REQUEST["tableName"];
    $lastIndex = $_REQUEST["lastIndex"];
    $password = $_REQUEST["password"];
    $you = $_REQUEST["username"];

    require_once("../DB.php");

    $check_query = "SELECT * FROM users WHERE BINARY username='" . $you . "' AND password='" . $password . "'";
    $check_result = mysqli_query($conn, $check_query);
    if ($check_result) {
        if (mysqli_num_rows($check_result) == 0) {
            echo "Unknown Error";
        } else {
            $query = "SELECT * FROM " . $tablename . "";
            $result = mysqli_query($conn, $query);
            if ($result) {
                if (mysqli_num_rows($result)) {
                    $actualLast = mysqli_num_rows($result);
                    if ($actualLast == $lastIndex) {
                        echo "no";
                    } else {
                        $row;
                        $tempIndex = 1;
                        while ($tempIndex <= $actualLast) {
                            $row = mysqli_fetch_row($result);
                            $tempIndex++;
                        }
                        $fromwho = $row[0];
                        $message = $row[1];

                        $res = array("fromwho" => $fromwho, "message" => $message);
                        echo json_encode($res);
                    }
                } else {
                    echo "No Messages";
                }
            } else {
                echo "Unknown Error";
            }
        }
    } else {
        echo "Unknown Error";
    }
    mysqli_close($conn);
} else {
    echo "Unknown Error";
}
