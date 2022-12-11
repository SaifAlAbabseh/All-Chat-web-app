<?php
session_start();
if (isset($_SESSION) && isset($_SESSION["who"]) && isset($_REQUEST) && isset($_REQUEST["group_id"]) && isset($_REQUEST["user"]) && isset($_SESSION["isGood" . $_REQUEST["group_id"]]) && $_SESSION["isGood" . $_REQUEST["group_id"]]) {
    require_once("DB.php");
    $you = $_SESSION["who"];
    $withwho = $_REQUEST["user"];

    $check_query = "SELECT user1 FROM friends WHERE BINARY user1='" . $you . "' AND BINARY user2='" . $withwho . "' OR BINARY user1='" . $withwho . "' AND BINARY user2='" . $you . "'";
    $check_result = mysqli_query($conn, $check_query);
    if ($check_result) {
        if (mysqli_num_rows($check_result)) {
            $group_table_name = "g" . $_REQUEST["group_id"] . "_users";
            $check_if_already_exists = "SELECT * FROM $group_table_name WHERE BINARY username='" . $withwho . "'";
            $check_if_already_exists_result = mysqli_query($conn, $check_if_already_exists);
            if ($check_if_already_exists_result) {
                if (mysqli_num_rows($check_if_already_exists_result)) {
                    echo "<script>alert('Already a member..');</script>";
                } else {
                    $query = "INSERT INTO $group_table_name VALUES('" . $_REQUEST["user"] . "', 'member')";
                    $result = mysqli_query($conn, $query);
                    if ($result) {
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