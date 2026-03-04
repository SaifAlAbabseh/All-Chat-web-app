<?php
if (isset($_REQUEST) && isset($_REQUEST["check"]) && $_REQUEST["check"] == "fromMobile1090" && isset($_REQUEST["username"]) && isset($_REQUEST["password"]) && isset($_REQUEST["tableName"])) {
    $tablename = $_REQUEST["tableName"];
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
        $query = "SELECT * FROM " . $tablename . "";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($result)) {
            $res = array();

            while ($row = mysqli_fetch_row($result)) {
                $json = array("fromwho" => $row[0], "message" => $row[1]);
                array_push($res, $json);
            }
            echo json_encode($res);
        } else {
            echo "No Messages";
        }
    }
}

mysqli_close($conn);
