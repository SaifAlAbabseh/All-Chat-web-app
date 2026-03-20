<?php 

session_start();
if(isset($_SESSION) && isset($_SESSION["who"]) && isset($_REQUEST) && isset($_REQUEST["f"]) && isset($_REQUEST["tname"])){
    require_once("DB.php");
    require_once("common.php");
    $fName=$_REQUEST["f"];
    $you=$_SESSION["who"];
    $tablename=$_REQUEST["tname"];
    $query="SELECT * FROM friends WHERE BINARY user1=? AND BINARY user2=? OR BINARY user1=? AND BINARY user2=?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ssss", $you, $fName, $fName, $you);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if($result){
        if(mysqli_num_rows($result)){
            $query2="SELECT available FROM users WHERE BINARY username=?";
            $stmt2 = mysqli_prepare($conn, $query2);
            $f = $_REQUEST["f"];
            mysqli_stmt_bind_param($stmt2, "s", $f);
            mysqli_stmt_execute($stmt2);
            $result2 = mysqli_stmt_get_result($stmt2);
            if($result2){
                if(mysqli_num_rows($result2)){
                    $row=mysqli_fetch_row($result2);
                    $ava="".$row[0];


                    $query3="SELECT * FROM ".$tablename."";
                    $stmt3 = mysqli_prepare($conn, $query3);
                    mysqli_stmt_execute($stmt3);
                    $result3 = mysqli_stmt_get_result($stmt3);
                    if($result3){
                        if(mysqli_num_rows($result3)){
                            $date="";
                            $pos="";
                            $message="";
                            $lastwho="";
                            while($row2=mysqli_fetch_row($result3)){
                                $date="".$row2[3];
                                $pos="".$row2[2];
                                $message="".$row2[1];
                                $lastwho="".$row2[0];
                            }
                            $messageDecoded=nl2br(formatMessage(urldecode($message)));
                            $arr=array("ava" => $ava, "date" => $date, "pos" => $pos, "message" => $messageDecoded, "lastwho" => $lastwho);
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
    echo "error";
}



?>