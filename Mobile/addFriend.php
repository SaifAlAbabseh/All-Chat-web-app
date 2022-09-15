<?php
if(isset($_REQUEST) && isset($_REQUEST["check"]) && $_REQUEST["check"]=="fromMobile1090" && isset($_REQUEST["username"]) && isset($_REQUEST["password"]) && isset($_REQUEST["friendUsername"])){
    $you=$_REQUEST["username"];
    $fusername=$_REQUEST["friendUsername"];
    
    require_once("../DB.php");
    
    $query="SELECT * FROM users WHERE BINARY username='".$fusername."'";
    $result=mysqli_query($conn,$query);
    if($result){
        if(mysqli_num_rows($result)){
            $query2="SELECT * FROM friends WHERE BINARY user1='" . $you . "' AND BINARY user2='" . $fusername . "' OR BINARY user1='" . $fusername . "' AND BINARY user2='" . $you . "'";
            $result2=mysqli_query($conn,$query2);
            if($result2){
                if(mysqli_num_rows($result2)){
                    echo "Already a friend";
                }
                else{
                    $query3="INSERT INTO friends VALUES ('".$you."','".$fusername."')";
                    if(mysqli_query($conn,$query3)){
                        $tablename = "" . $you . "" . $fusername;
                        $query4 = "CREATE TABLE " . $tablename . " (fromwho varchar(1000) , message varchar(1000),pos varchar(1000))";
                        if(mysqli_query($conn, $query4)){
                            echo "ok";
                        }
                        else{
                            echo "Unknown Error";
                        }
                    }
                    else{
                        echo "Unknown Error";
                    }
                }
            }
            else{
                echo "Unknown Error";
            }
        }
        else{
            echo "Username not found";
        }
    }
    else{
        echo "Unknown Error";
    }
    mysqli_close($conn);
}
else{
    header("Location:../index.php");
}
?>