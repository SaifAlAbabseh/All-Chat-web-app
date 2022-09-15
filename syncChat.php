<?php 

session_start();
if(isset($_SESSION) && isset($_SESSION["who"]) && isset($_REQUEST) && isset($_REQUEST["f"]) && isset($_REQUEST["tname"])){
    require_once("DB.php");
    $fName=$_REQUEST["f"];
    $you=$_SESSION["who"];
    $tablename=$_REQUEST["tname"];
    $query="SELECT * FROM friends WHERE BINARY user1='" . $you . "' AND BINARY user2='" . $fName . "' OR BINARY user1='" . $fName . "' AND BINARY user2='" . $you . "'";
    $result=mysqli_query($conn,$query);
    if($result){
        if(mysqli_num_rows($result)){
            $query2="SELECT available FROM users WHERE BINARY username='".$_REQUEST["f"]."'";
            $result2=mysqli_query($conn,$query2);
            if($result2){
                if(mysqli_num_rows($result2)){
                    $row=mysqli_fetch_row($result2);
                    $ava="".$row[0];


                    $query3="SELECT * FROM ".$tablename."";
                    $result3=mysqli_query($conn,$query3);
                    if($result3){
                        if(mysqli_num_rows($result3)){
                            $pos="";
                            $message="";
                            $lastwho="";
                            while($row2=mysqli_fetch_row($result3)){
                                $pos="".$row2[2];
                                $message="".$row2[1];
                                $lastwho="".$row2[0];
                            }
                            $messageDecoded=nl2br(htmlspecialchars(urldecode($message)));
                            $arr=Array("ava" => $ava, "pos" => $pos, "message" => $messageDecoded, "lastwho" => $lastwho);
                            echo json_encode($arr);
                        }
                        else{
                            echo "nomore*".$ava;
                        }
                    }
                }
            }
            else{
                echo "error";
            }
        }
        else{
            echo "nomore";
        }
    }
}
else{
    header("Location:../All_Chat/");
}



?>