<?php
if(isset($_REQUEST) && isset($_REQUEST["check"]) && $_REQUEST["check"]=="fromMobile1090" && isset($_REQUEST["username"]) && isset($_REQUEST["password"])){
    $username=$_REQUEST["username"];
    $password=$_REQUEST["password"];
    
    require_once("../DB.php");
    $query="SELECT * FROM users WHERE BINARY username='".$username."'";
    $result=mysqli_query($conn,$query);
    if($result){
        if(mysqli_num_rows($result)){
            echo "Username is already in use";
        }
        else{
            $query2="INSERT INTO users VALUES ('".$username."','".$password."','defaultUser','0')";
            if(mysqli_query($conn,$query2)){
                echo "Successfully created account";
            }
            else{
                echo "Unknown Error";
            }
        }
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