<?php 
session_start();
if(isset($_REQUEST) && isset($_REQUEST["name"]) && isset($_SESSION) && isset($_SESSION["who"])){
    $fName=$_REQUEST["name"];
    $you=$_SESSION["who"];
    require_once("../../DB.php");
    $query="DELETE FROM friends WHERE BINARY user1='" . $you . "' AND BINARY user2='" . $fName . "' OR BINARY user1='" . $fName . "' AND BINARY user2='" . $you . "'";
    if(mysqli_query($conn,$query)){
        $r1="".$you."".$fName;
        $r2="".$fName."".$you;
        $query2="DROP TABLE IF EXISTS ".$r1.",".$r2."";
        if(mysqli_query($conn,$query2)){}
    }
    mysqli_close($conn);
    header("Location:../");
}
else{
    header("Location:../../");
}
?>