<?php
session_start();
if (isset($_SESSION) && isset($_SESSION["who"]) && isset($_REQUEST) && isset($_REQUEST["group_id"]) && isset($_REQUEST["user"]) && isset($_SESSION["isGood" . $_REQUEST["group_id"]]) && $_SESSION["isGood" . $_REQUEST["group_id"]]) {
    require_once("DB.php");
    $you = $_SESSION["who"];
    $withwho = $_REQUEST["user"];

    $check_query = "SELECT user1 FROM friends WHERE BINARY user1=? AND BINARY user2=? OR BINARY user1=? AND BINARY user2=?";
    $stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($stmt, "ssss", $you, $withwho, $withwho, $you);
    mysqli_stmt_execute($stmt);
    $check_result = mysqli_stmt_get_result($stmt);
    if ($check_result) {
        if (mysqli_num_rows($check_result)) {
            $group_table_name = "g" . $_REQUEST["group_id"] . "_users";
            $check_if_already_exists = "SELECT * FROM $group_table_name WHERE BINARY username=?";
            $stmt2 = mysqli_prepare($conn, $check_if_already_exists);
            mysqli_stmt_bind_param($stmt2, "s", $withwho);
            mysqli_stmt_execute($stmt2);
            $check_if_already_exists_result = mysqli_stmt_get_result($stmt2);
            if ($check_if_already_exists_result) {
                if (mysqli_num_rows($check_if_already_exists_result)) {
                    echo "<script>alert('Already a member..');</script>";
                } else {
                    $query = "INSERT INTO $group_table_name VALUES(?, 'member')";
                    $stmt3 = mysqli_prepare($conn, $query);
                    $user_to_add = $_REQUEST["user"];
                    mysqli_stmt_bind_param($stmt3, "s", $user_to_add);
                    if (mysqli_stmt_execute($stmt3)) {
                        header("Location:Main/Group/?group_id=".$_REQUEST["group_id"]);
                    } else {
                        echo "<script>alert('Unknown Error..');</script>";
                    }
                }
            } else {
                echo "<script>alert('Unknown Error..');</script>";
            }
        } else {
            echo "<script>alert('Not a friend! pls refresh the page..');</script>";
        }
    } else {
        echo "<script>alert('Unknown Error..');</script>";
    }
    mysqli_close($conn);
    echo "<script>window.location.replace('Main/Group/?group_id=".$_REQUEST["group_id"]."');</script>";
} else {
    echo "<script>alert('Unknown Error..');</script>";
}

?>