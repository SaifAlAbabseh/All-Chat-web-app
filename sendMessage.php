<?php
session_start();
if (!(isset($_SESSION) && isset($_SESSION["who"]) && isset($_REQUEST) && isset($_REQUEST["message"]) && isset($_REQUEST["lastI"]) && isset($_REQUEST["tname"]))) {
    echo "Unknown Error..";
}
else{
    $message=trim($_REQUEST["message"]);
    $message = $message;
    $you=$_SESSION["who"];
    $tname=$_REQUEST["tname"];
    $lastI=$_REQUEST["lastI"];
    if($message!=""){
        if(strlen($message)<=700){
            sendMessage($message,$you,$tname,$lastI);
        }
        else{
            echo "Error : Message Length More Than 700";
        }
    }
    else{
        echo "Error : Field Cannot Be Empty";
    }
}
function sendMessage($message,$you,$tname,$lastI){
    $lastI++;
    require_once("DB.php");
    $messageEncoded=urlencode($message);
    $query="INSERT INTO ".$tname." VALUES ('".$you."','".$messageEncoded."','".$lastI."')";
    if(mysqli_query($conn,$query)){
        echo "ok";
    }
    else{
        echo "Connection Error";
    }
    mysqli_close($conn);
}
?>