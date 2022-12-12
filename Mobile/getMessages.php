<?php
if (isset($_REQUEST) && isset($_REQUEST["check"]) && $_REQUEST["check"] == "fromMobile1090" && isset($_REQUEST["username"]) && isset($_REQUEST["password"]) && isset($_REQUEST["tableName"])) {
    $tablename = $_REQUEST["tableName"];
    $username = $_REQUEST["username"];
    $password = $_REQUEST["password"];

    require_once("../DB.php");

    $query = "SELECT * FROM users WHERE BINARY username='" . $username . "' AND password='" . $password . "'";
    $result = mysqli_query($conn, $query);
    if ($result) {
        if (mysqli_num_rows($result) == 0) {
            echo "Unknown Error";
        } else {
            $query = "SELECT * FROM " . $tablename . "";
            $result = mysqli_query($conn, $query);
            if ($result) {
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
            } else {
                echo "Unknown Error";
            }
        }
    }

    mysqli_close($conn);
} else {
    echo "Unknown Error";
}
