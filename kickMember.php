<?php 

session_start();
if (!(isset($_SESSION) && isset($_SESSION["who"]) && isset($_REQUEST) && isset($_REQUEST["group_id"]) && isset($_REQUEST["member"]) && isset($_SESSION["isGood".$_REQUEST["group_id"]]) && $_SESSION["isGood".$_REQUEST["group_id"]])) {
    session_destroy();
    header("Location:index.php");
}
else{
    $group_id = $_REQUEST["group_id"];
    $username = $_SESSION["who"];
    $member_to_kick = $_REQUEST["member"];

    require_once("DB.php");
    $group_table_name = "g".$group_id."_users"; 
    $remove_query = "DELETE FROM $group_table_name WHERE BINARY username='".$member_to_kick."'";
    $remove_result = mysqli_query($conn, $remove_query);
    mysqli_close($conn);
    if(!$remove_result){
        die("Error");
    }
    else{
        header("Location:Main/Group/?group_id=".$_REQUEST["group_id"]);
    }
}

?>