<?php

if(isset($_REQUEST) && isset($_REQUEST["check"]) && $_REQUEST["check"]=="fromMobile1090" && isset($_REQUEST["username"]) && isset($_REQUEST["password"]) && isset($_REQUEST["tableName"]) && isset($_REQUEST["message"]) && isset($_REQUEST["lastIndex"]) ){
    $tableName=$_REQUEST["tableName"];
    $fromWho=$_REQUEST["username"];
    $message=$_REQUEST["message"];
    $lastIndex=$_REQUEST["lastIndex"];

    $encodedMessage=urlencode($message);

    require_once("../DB.php");
    
    $query="INSERT INTO ".$tableName." VALUES ('".$fromWho."','".$encodedMessage."','".($lastIndex+1)."')";

    if(mysqli_query($conn,$query)){
        echo "ok";
    }
    else{
        echo "Unknown Error";
    }
    mysqli_close($conn);
}
else{
    header("../Location:index.php");
}

?>