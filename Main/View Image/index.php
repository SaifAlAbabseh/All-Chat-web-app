<?php 
session_start();
if(isset($_SESSION) && isset($_SESSION["who"]) && isset($_REQUEST) && isset($_REQUEST["u"])){
    require_once("../../DB.php");
    $query="SELECT picture FROM users WHERE BINARY username='".$_REQUEST["u"]."'";
    $result=mysqli_query($conn,$query);
    mysqli_close($conn);
    $temp;
    if($result){
        if(mysqli_num_rows($result)){
            $row=mysqli_fetch_row($result);
            $temp="".$row[0];
        }
    }
    $path="../../Extra/styles/images/".$temp.".png";
    header('Content-Type: image/png');
    readfile($path);
}
else{
    header("Location:../../");
}
?>