<?php
if(isset($_REQUEST) && isset($_REQUEST["check"]) && $_REQUEST["check"]=="fromMobile1090" && isset($_REQUEST["username"]) && isset($_REQUEST["password"])){
    $username=$_REQUEST["username"];
    $password=$_REQUEST["password"];
    
    require_once("../DB.php");
    $query="SELECT * FROM users WHERE BINARY username=?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if($result){
        if(mysqli_num_rows($result)){
            echo "Username is already in use";
        }
        else{
            $query2="INSERT INTO users VALUES (?,?,?,?)";
            $stmt2 = mysqli_prepare($conn, $query2);
            $pic = 'defaultUser';
            $ava = '0';
            mysqli_stmt_bind_param($stmt2, "ssss", $username, $password, $pic, $ava);
            if(mysqli_stmt_execute($stmt2)){
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
    echo "Unknown Error";
}
?>