<?php 
session_start();
if((isset($_REQUEST) && isset($_REQUEST["u"])) && 
    (((isset($_SESSION) && isset($_SESSION["who"]) && isset($_REQUEST))) || 
    (isset($_REQUEST["token"]) && isset($_REQUEST["token"]) != ""))) {
        require_once("../../DB.php");
        if(isset($_REQUEST["token"])) {
            if($_REQUEST["token"] == "")
                die("Not valid token..");
            $token = $_REQUEST["token"];
            $query = "SELECT * FROM users WHERE view_image_token='".$token."'";
            $result = mysqli_query($conn, $query);
            if(!($result && mysqli_num_rows($result) > 0)) {
                die("Not valid token..");
            }
        }
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
        $path="../../Extra/styles/images/users images/".$temp.".png";
        header('Content-Type: image/png');
        readfile($path);
}
else{
    die("Not authorized..");
}
?>