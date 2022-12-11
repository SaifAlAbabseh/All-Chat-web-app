<?php
session_start();
if (isset($_SESSION) && isset($_SESSION["who"]) && isset($_REQUEST) && isset($_REQUEST["group_id"])) {
    require_once("../../DB.php");
    $group_table_name = "g" . $_REQUEST["group_id"] . "_users";
    $check_query = "SELECT g.group_image FROM all_chat_groups g INNER JOIN $group_table_name gu WHERE BINARY gu.username='" . $_SESSION["who"] . "' AND g.group_id='" . $_REQUEST["group_id"] . "'";
    $check_result = mysqli_query($conn, $check_query);
    $temp;
    if ($check_result && mysqli_num_rows($check_result)) {
        $row = mysqli_fetch_row($check_result);
        $temp = "" . $row[0];
        $path = "../../Extra/styles/images/groups images/" . $temp;
        header('Content-Type: image/png');
        readfile($path);
    } else {
        die("Error");
    }
    mysqli_close($conn);
} else {
    header("Location:../../");
}
