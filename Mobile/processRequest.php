<?php


if (isset($_REQUEST) && isset($_REQUEST["check"]) && $_REQUEST["check"] == "fromMobile1090" && isset($_REQUEST["process"]) && isset($_REQUEST["username"]) && isset($_REQUEST["password"]) && isset($_REQUEST["requestId"]) && isset($_REQUEST["requesterUsername"])) {
    $you = $_REQUEST["username"];
    $requestId = $_REQUEST["requestId"];
    $password = $_REQUEST["password"];
    $requesterUsername = $_REQUEST["requesterUsername"];
    $process = $_REQUEST["process"];

    require_once("../DB.php");

    $check_query = "SELECT * FROM users WHERE BINARY username=? AND password=?";
    $stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($stmt, "ss", $you, $password);
    mysqli_stmt_execute($stmt);
    $check_result = mysqli_stmt_get_result($stmt);
    if (mysqli_num_rows($check_result) == 0) {
        echo "Unknown Error";
    } else {
        $query = "SELECT * FROM friend_requests WHERE request_id=? AND requester=? AND requested_user=?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sss", $requestId, $requesterUsername, $you);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($result) == 0) {
            echo "Unknown Error";
        } else {
            $query = "DELETE FROM friend_requests WHERE request_id=?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "s", $requestId);
            mysqli_stmt_execute($stmt);
            if (mysqli_affected_rows($conn) > 0) {
                if ($process == "accept") {
                    // Actually to be a freind
                    $query = "INSERT INTO friends VALUES (?,?)";
                    $stmt = mysqli_prepare($conn, $query);
                    mysqli_stmt_bind_param($stmt, "ss", $you, $requesterUsername);
                    mysqli_stmt_execute($stmt);
                    $tablename = "" . $you . "" . $requesterUsername;
                    $query = "CREATE TABLE " . $tablename . " (fromwho varchar(1000) , message varchar(1000),pos varchar(1000), whenSent DATETIME DEFAULT CURRENT_TIMESTAMP)";
                    $stmt = mysqli_prepare($conn, $query);
                    mysqli_stmt_execute($stmt);
                    echo "Success";
                } else if($process == "reject"){
                    echo "Success";
                }
            } else {
                echo "Unknown Error";
            }
        }
        mysqli_close($conn);
        exit(0);
    }
} else {
    echo "Error";
}
