<?php 
session_start();
if (!(isset($_SESSION) && isset($_SESSION["who"]) && isset($_REQUEST) && isset($_REQUEST["group_id"]))) {
    session_destroy();
    header("Location:../../");
}
else{
    $group_id = $_REQUEST["group_id"];
    $username = $_SESSION["who"];

    require_once("../../../DB.php");
    $group_table_name = "g".$group_id."_users"; 
    $remove_query = "DELETE FROM $group_table_name WHERE BINARY username=?";
    $stmt = mysqli_prepare($conn, $remove_query);
    mysqli_stmt_bind_param($stmt, "s", $username);
    $remove_result = mysqli_stmt_execute($stmt);
    if($remove_result){
        header("Location:../../");
    }
    else{
        die("Error");
    }
    mysqli_close($conn);
}
?>