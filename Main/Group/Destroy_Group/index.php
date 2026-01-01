<?php 
session_start();
if (!(isset($_SESSION) && isset($_SESSION["who"]) && isset($_REQUEST) && isset($_REQUEST["group_id"]) && isset($_SESSION["isGood".$_REQUEST["group_id"]]) && $_SESSION["isGood".$_REQUEST["group_id"]])) {
    session_destroy();
    header("Location:../../../");
}
else{
    $group_id = $_REQUEST["group_id"];
    $username = $_SESSION["who"];

    require_once("../../../DB.php");
    $first_group_table_name = "g".$group_id."_users"; 
    $second_group_table_name = "g".$group_id; 

    $first_query = "DELETE FROM all_chat_groups WHERE BINARY leader_username='".$username."' AND group_id='".$group_id."'";
    $first_result = mysqli_query($conn, $first_query);
    if($first_result){
        $second_query = "DROP TABLE IF EXISTS $first_group_table_name, $second_group_table_name";
        $second_result = mysqli_query($conn, $second_query);
        if($second_result){
            $image_path = "../../../Extra/styles/images/groups_images/i" . $group_id . ".png";
            if(unlink($image_path)){
                header("Location:../../");
            }
            else{
                header("Location:../../../");
            }
        }
        else{
            header("Location:../../../");
        }
    }
    else{
        header("Location:../../../");
    }
    unset($_SESSION["isGood".$_REQUEST["group_id"]]);
    mysqli_close($conn);
}
?>