<?php

session_start();
if (isset($_SESSION) && isset($_SESSION["who"]) && isset($_REQUEST) && isset($_REQUEST["group_id"])) {
    require_once("DB.php");
    require_once("common.php");
    $you = $_SESSION["who"];
    $group_id = $_REQUEST["group_id"];
    $check_table_name = "g" . $group_id . "_users";
    $query = "SELECT * FROM $check_table_name WHERE BINARY username='" . $you . "'";
    $result = mysqli_query($conn, $query);
    if ($result) {
        if (mysqli_num_rows($result)) {
            $group_table_name = "g" . $group_id;
            $query3 = "SELECT * FROM " . $group_table_name . "";
            $result3 = mysqli_query($conn, $query3);
            if ($result3) {
                if (mysqli_num_rows($result3)) {
                    $date = "";
                    $pos = "";
                    $message = "";
                    $lastwho = "";
                    while ($row2 = mysqli_fetch_row($result3)) {
                        $date="".$row2[3];
                        $pos = "" . $row2[2];
                        $message = "" . $row2[1];
                        $lastwho = "" . $row2[0];
                    }
                    $messageDecoded = nl2br(formatMessage(htmlspecialchars(urldecode($message))));
                    $arr = array("date" => $date, "pos" => $pos, "message" => $messageDecoded, "lastwho" => $lastwho);
                    echo json_encode($arr);
                    exit;
                }
                else{
                    echo "empty";
                    exit;
                }
            } else {
                echo "error";
                exit;
            }
        } else {
            echo "nomore";
            exit;
        }
    }
    else {
        echo "error";
        exit;
    }
} else {
    echo "error";
    exit;
}
