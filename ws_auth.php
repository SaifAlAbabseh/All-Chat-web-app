<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION["who"]) && !isset($_SESSION["ws_token"])) {
    require_once __DIR__ . "/DB.php";
    $token = bin2hex(random_bytes(32));
    $username = $_SESSION["who"];
    
    $query = "INSERT INTO ws_tokens (token, username) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ss", $token, $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        $_SESSION["ws_token"] = $token;
    }
}
?>
