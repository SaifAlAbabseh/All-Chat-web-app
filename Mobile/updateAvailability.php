<?php
if(isset($_REQUEST) && isset($_REQUEST["check"]) && $_REQUEST["check"]=="fromMobile1090" && isset($_REQUEST["username"]) && isset($_REQUEST["password"]) && isset($_REQUEST["which"])){
    $username=$_REQUEST["username"];
    $which=$_REQUEST["which"];

    require_once("../DB.php");
    if($which==1){
        $query="UPDATE users SET available='0' WHERE BINARY username='".$username."'";
        if(!mysqli_query($conn,$query)){
            echo "Unknown Error";
        }
        else{
            echo "ok";
        }
    }
    else if($which==2){
        $query="UPDATE users SET available='1' WHERE BINARY username='".$username."'";
        if(!mysqli_query($conn,$query)){
            echo "Unknown Error";
        }
        else{
            echo "ok";
        }
    }
    mysqli_close($conn);
}
else{
    header("Location:../index.php");
}
