<?php

if(isset($_REQUEST) && isset($_REQUEST["check"]) && $_REQUEST["check"]=="fromMobile1090" && isset($_REQUEST["username"]) && isset($_REQUEST["password"]) && isset($_REQUEST["tableName"]) && isset($_REQUEST["message"]) && isset($_REQUEST["lastIndex"]) ){
    $tableName=$_REQUEST["tableName"];
    $fromWho=$_REQUEST["username"];
    $message=$_REQUEST["message"];
    $lastIndex=$_REQUEST["lastIndex"];

    $username = $_REQUEST["username"];
    $password = $_REQUEST["password"];

    $encodedMessage=urlencode($message);

    require_once("../DB.php");

    $query = "SELECT * FROM users WHERE BINARY username='" . $username . "' AND password='" . $password . "'";
    $result = mysqli_query($conn, $query);
    if ($result) {
        if (mysqli_num_rows($result) == 0) {
            echo "Unknown Error";
        } else {
            $query="INSERT INTO ".$tableName."(fromwho, message, pos) VALUES ('".$fromWho."','".$encodedMessage."','".($lastIndex+1)."')";

            if(mysqli_query($conn,$query)){
                echo "ok";
            }
            else{
                echo "Unknown Error";
            }
        }
    }
    mysqli_close($conn);
}
else{
    echo "Unknown Error";
}
