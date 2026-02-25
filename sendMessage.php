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
    
    try {
        $query="INSERT INTO ".$tname."(fromwho, message, pos) VALUES (?,?,?)";
        $stmt = mysqli_prepare($conn, $query);
        if ($stmt === false) {
            echo "Error: " . mysqli_error($conn);
            mysqli_close($conn);
            return;
        }
        mysqli_stmt_bind_param($stmt, "sss", $you, $messageEncoded, $lastI);
        if(mysqli_stmt_execute($stmt)){
            echo "ok";
        }
        else{
            echo "Error: " . mysqli_stmt_error($stmt);
        }
        mysqli_close($conn);
    } catch (Exception $e) {
        echo "Error:  Does not exist anymore";
        if(isset($conn)) {
            mysqli_close($conn);
        }
    }
}
?>