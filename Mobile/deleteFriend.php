<?php
if (isset($_REQUEST) && isset($_REQUEST["check"]) && $_REQUEST["check"] == "fromMobile1090" && isset($_REQUEST["username"]) && isset($_REQUEST["password"]) && isset($_REQUEST["friendUsername"])) {
    $you = $_REQUEST["username"];
    $fusername = $_REQUEST["friendUsername"];
    $password = $_REQUEST["password"];
    require_once("../DB.php");
    $check_query = "SELECT * FROM users WHERE BINARY username=? AND password=?";
    $stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($stmt, "ss", $you, $password);
    mysqli_stmt_execute($stmt);
    $check_result = mysqli_stmt_get_result($stmt);
    if (mysqli_num_rows($check_result) == 0) {
        echo "Unknown Error";
    } else {
        $query = "DELETE FROM friends WHERE BINARY user1=? AND BINARY user2=? OR BINARY user1=? AND BINARY user2=?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ssss", $you, $fusername, $fusername, $you);
        if (mysqli_stmt_execute($stmt)) {
            $r1 = "" . $you . "" . $fusername;
            $r2 = "" . $fusername . "" . $you;
            $query2 = "DROP TABLE IF EXISTS " . $r1 . "," . $r2 . "";
            $stmt = mysqli_prepare($conn, $query2);
            if ($stmt && mysqli_stmt_execute($stmt)) {
                echo "Deleted Friend";
            }
        }
        mysqli_close($conn);
    }
}
