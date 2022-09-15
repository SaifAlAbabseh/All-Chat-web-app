<?php
if(isset($_REQUEST) && isset($_REQUEST["check"]) && $_REQUEST["check"]=="fromMobile1090" && isset($_REQUEST["username"]) && isset($_REQUEST["password"])){
    $username=$_REQUEST["username"];

    require_once("../DB.php");
    $query="SELECT * FROM friends WHERE BINARY user1='".$username."' OR BINARY user2='".$username."'";
    $result=mysqli_query($conn,$query);
    if($result){
        if (mysqli_num_rows($result)){
            $after="";
            while ($row = mysqli_fetch_row($result)){
                $user1 = $row[0];
                $user2 = $row[1];
                $get;
                $temp;
                if ($user1 == $username) {
                    $get = "SELECT * FROM users WHERE BINARY username='" . $user2 . "'";
                    $temp=1;
                } else if ($user2 == $username) {
                    $get = "SELECT * FROM users WHERE BINARY username='" . $user1 . "'";
                    $temp=2;
                }
                $result2 = mysqli_query($conn, $get);
                if (mysqli_num_rows($result2)){
                    while ($row2 = mysqli_fetch_row($result2)){
                        $ava;
                        if ($row2[3] == "0") {
                            $ava = "0";
                        } else if ($row2[3] == "1") {
                            $ava = "1";
                        }
                        $after.="".$row2[0]."|".$row2[2]."|".$row2[3]."|".$temp;
                    }
                    $after.="&";
                }
                else{
                    echo "Unknown Error";
                }
            }
            echo $after;
        }
        else{
            echo "No Friends";
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