<?php
if(isset($_REQUEST) && isset($_REQUEST["check"]) && $_REQUEST["check"]=="fromMobile1090" && isset($_REQUEST["username"]) && isset($_REQUEST["password"]) && isset($_REQUEST["friendUsername"])){
    $friendUsername=$_REQUEST["friendUsername"];
    $username=$_REQUEST["username"];

    require_once("../DB.php");

    $query="SELECT * FROM friends WHERE BINARY user1='".$username."' AND BINARY user2='".$friendUsername."' OR BINARY user1='".$friendUsername."' AND BINARY user2='".$username."'";
    $result=mysqli_query($conn,$query);
    if($result){
        if(mysqli_num_rows($result)){
            $query2="SELECT available,picture FROM users WHERE BINARY username='".$friendUsername."'";
            $result2=mysqli_query($conn,$query2);
            if($result2){
                if(mysqli_num_rows($result2)){
                    $row=mysqli_fetch_row($result2);

                    $ava=$row[0];
                    $picName=$row[1];

                    echo "".$ava."|".$picName;
                }
            }
            else{
                echo "Unknown Error";
            }
        }
        else{
            echo "Not a friend";
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