<?php
session_start();
if(isset($_SESSION) && isset($_SESSION["who"])){
    require_once("../../DB.php");
    $query="UPDATE users SET available='0' WHERE BINARY username=?";
    $stmt = mysqli_prepare($conn, $query);
    $who = $_SESSION["who"];
    mysqli_stmt_bind_param($stmt, "s", $who);
    mysqli_stmt_execute($stmt);
    mysqli_close($conn);
    session_destroy();
}
header("Location:../../");
?>