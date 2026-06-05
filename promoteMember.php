<?php 

require_once("./common.php");

session_start();
if (!(isset($_SESSION) && isset($_SESSION["who"]) && isset($_REQUEST) && isset($_REQUEST["group_id"]) && isset($_REQUEST["member"]) && isset($_SESSION["isGood".$_REQUEST["group_id"]]) && $_SESSION["isGood".$_REQUEST["group_id"]])) {
    session_destroy();
    header("Location:./index.php");
}
else{
    $group_id = $_REQUEST["group_id"];
    $username = $_SESSION["who"];
    $member_to_promote = $_REQUEST["member"];

    require_once("DB.php");
    $group_table_name = "g".$group_id."_users"; 
    
    // Validate that the requestor is actually a leader
    $check_query = "SELECT user_type FROM $group_table_name WHERE BINARY username=?";
    $stmt1 = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($stmt1, "s", $username);
    mysqli_stmt_execute($stmt1);
    $check_result = mysqli_stmt_get_result($stmt1);
    if ($check_result && mysqli_num_rows($check_result)) {
        $row = mysqli_fetch_row($check_result);
        if ($row[0] === "leader") {
            // Promote target member
            $update_query = "UPDATE $group_table_name SET user_type='leader' WHERE BINARY username=?";
            $stmt2 = mysqli_prepare($conn, $update_query);
            mysqli_stmt_bind_param($stmt2, "s", $member_to_promote);
            mysqli_stmt_execute($stmt2);
        }
    }
    
    mysqli_close($conn);
    header("Location:Main/Group/?group_id=".$_REQUEST["group_id"]);
}

?>
