<?php 
session_start();
if(isset($_REQUEST) && isset($_REQUEST["name"]) && isset($_SESSION) && isset($_SESSION["who"])){
    $fName=$_REQUEST["name"];
    $you=$_SESSION["who"];
    require_once("../../DB.php");
    $query="DELETE FROM friends WHERE BINARY user1=? AND BINARY user2=? OR BINARY user1=? AND BINARY user2=?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ssss", $you, $fName, $fName, $you);
    if(mysqli_stmt_execute($stmt)){
        $r1="".$you."".$fName;
        $r2="".$fName."".$you;
        $query2="DROP TABLE IF EXISTS ".$r1.",".$r2."";
        $stmt = mysqli_prepare($conn, $query2);
        if ($stmt && mysqli_stmt_execute($stmt)) {}
    }
    mysqli_close($conn);
    header("Location:../");
}
else{
    header("Location:../../");
}
?>