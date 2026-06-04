<?php
if(isset($_REQUEST) && isset($_REQUEST["check"]) && $_REQUEST["check"]=="fromMobile1090" && isset($_REQUEST["username"]) && isset($_REQUEST["password"])){
    $username=$_REQUEST["username"];
    $password=$_REQUEST["password"];
    
    require_once("../DB.php");
    $query="SELECT password FROM users WHERE BINARY username=?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if($result){
        if(mysqli_num_rows($result)){
            $row = mysqli_fetch_assoc($result);
            $db_pass = $row['password'];
            // Check if plaintext matches BCrypt, or if plaintext matches MD5, or if provided password is the MD5 itself and matches MD5
            if(password_verify($password, $db_pass) || md5($password) === $db_pass || $password === $db_pass) {
                echo "ok";
                // Transparent migration if needed
                if(md5($password) === $db_pass || $password === $db_pass) {
                    // if $password is plaintext, we can migrate it
                    if (md5($password) === $db_pass) {
                        $new_hash = password_hash($password, PASSWORD_BCRYPT);
                        $upd = "UPDATE users SET password=? WHERE BINARY username=?";
                        $upd_stmt = mysqli_prepare($conn, $upd);
                        mysqli_stmt_bind_param($upd_stmt, "ss", $new_hash, $username);
                        mysqli_stmt_execute($upd_stmt);
                        mysqli_stmt_close($upd_stmt);
                    }
                }
            } else {
                echo "Username or password is incorrect";
            }
        }
        else{
            echo "User not found";
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