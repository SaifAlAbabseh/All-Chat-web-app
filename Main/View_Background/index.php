<?php
session_start();

if (isset($_SESSION) && isset($_SESSION["who"]) && isset($_REQUEST) && isset($_REQUEST["tname"]) && $_REQUEST["tname"] != "") {
    $tname = $_REQUEST["tname"];
    $who = $_SESSION["who"];
    
    $valid = false;
    
    // Check if it's a group chat background (starts with g and followed by digits)
    if (strpos($tname, "g") === 0 && is_numeric(substr($tname, 1))) {
        $group_id = substr($tname, 1);
        // User must be authenticated to view this group
        if (isset($_SESSION["isGood" . $group_id]) && $_SESSION["isGood" . $group_id] === true) {
            $valid = true;
        } else {
            // Check DB just in case
            require_once("../../DB.php");
            $group_table_name = $tname . "_users";
            $check_query = "SELECT gu.user_type FROM all_chat_groups g INNER JOIN $group_table_name gu WHERE BINARY gu.username=? AND g.group_id=?";
            $stmt = mysqli_prepare($conn, $check_query);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "ss", $who, $group_id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                if (mysqli_num_rows($result) > 0) {
                    $valid = true;
                }
            }
        }
    } else {
        // Personal chat: tname is formed by concatenating two usernames.
        // The current user must be one of them.
        if (strpos($tname, $who) !== false) {
            $valid = true;
        }
    }
    
    if ($valid) {
        $bg_path = "../../Extra/styles/images/chat_backgrounds/bg_" . $tname . ".png";
        if (file_exists($bg_path)) {
            header('Content-Type: image/png');
            readfile($bg_path);
            exit();
        } else {
            header("HTTP/1.0 404 Not Found");
            exit();
        }
    } else {
        header("HTTP/1.0 403 Forbidden");
        exit();
    }
} else {
    header("HTTP/1.0 403 Forbidden");
    exit();
}
?>
