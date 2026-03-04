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
        $query = "SELECT * FROM users WHERE BINARY username=?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $fusername);
        mysqli_stmt_execute($stmt);
        $isUserExistsResult = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($isUserExistsResult)) {
            $query2 = "SELECT * FROM friends WHERE BINARY user1=? AND BINARY user2=? OR BINARY user1=? AND BINARY user2=?";
            $stmt2 = mysqli_prepare($conn, $query2);
            mysqli_stmt_bind_param($stmt2, "ssss", $you, $fusername, $fusername, $you);
            mysqli_stmt_execute($stmt2);
            $result2 = mysqli_stmt_get_result($stmt2);
            if ($result2) {
                if (mysqli_num_rows($result2)) {
                    echo "Already a friend";
                } else {
                    $query = "SELECT * FROM friend_requests WHERE requester=? AND requested_user=?";
                    $stmt = mysqli_prepare($conn, $query);
                    $req = $_SESSION["who"];
                    $treq = $fusername;
                    mysqli_stmt_bind_param($stmt, "ss", $req, $treq);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    if (mysqli_num_rows($result)) {
                        echo "Already sent friend request";
                    } else {
                        $query = "INSERT INTO friend_requests(requester, requested_user) VALUES (?,?)";
                        $stmt = mysqli_prepare($conn, $query);
                        $req = $_REQUEST["username"];
                        $treq = $fusername;
                        mysqli_stmt_bind_param($stmt, "ss", $req, $treq);
                        mysqli_stmt_execute($stmt);

                        //Send email to receiver

                        require_once(dirname(__DIR__, 1) . '/mail.php');
                        $userEmail = mysqli_fetch_assoc($isUserExistsResult)["email"];
                        $temp = sendFriendRequestMail($conn, $userEmail, $fusername, $req);

                        ////////

                        echo "Sent Friend Request";

                    }
                }
            } else {
                echo "Unknown Error";
            }
        } else {
            echo "Username not found";
        }
    }

    mysqli_close($conn);
} else {
    echo "Unknown Error";
}
