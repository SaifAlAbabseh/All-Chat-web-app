<?php
session_start();
if (isset($_SESSION) && isset($_SESSION["who"])) {
    require_once("DB.php");
    $query = "SELECT * FROM friends WHERE BINARY user1=? OR BINARY user2=?";
    $stmt = mysqli_prepare($conn, $query);
    $who = $_SESSION["who"];
    mysqli_stmt_bind_param($stmt, "ss", $who, $who);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (mysqli_num_rows($result)) {
        while ($row = mysqli_fetch_row($result)) {
            $user1 = $row[0];
            $user2 = $row[1];
            $get;
            if ($user1 == $_SESSION["who"]) {
                $get = "SELECT * FROM users WHERE BINARY username=?";
                $username_to_get = $user2;
            } else if ($user2 == $_SESSION["who"]) {
                $get = "SELECT * FROM users WHERE BINARY username=?";
                $username_to_get = $user1;
            }
            $stmt2 = mysqli_prepare($conn, $get);
            mysqli_stmt_bind_param($stmt2, "s", $username_to_get);
            mysqli_stmt_execute($stmt2);
            $result2 = mysqli_stmt_get_result($stmt2);
            if (mysqli_num_rows($result2)) {
                while ($row2 = mysqli_fetch_row($result2)) {
                    $ava = $row2[3];
                    echo "
                                    <div class='friendRow'>
                                        <img src='View_Image/?u=" . $row2[0] . "' width='50px' height='50px' style='border-radius:50%;border:solid 4px " . (($ava == "0") ? "red" : "green") . "'/>
                                        <h2 class='friendUsername'>" . $row2[0] . "</h2> 
                                        <a href='Chat/?with=" . $row2[0] . "' class='link'>Chat</a>
                                        <a href='Delete_Friend/?name=" . $row2[0] . "' class='link'>Delete</a>
                                    </div>

                            ";
                }
            }
        }
    } else {
        echo "<h2 style='color:white'>No friends to show..</h2>";
    }
} else {
    session_destroy();
    echo "<h2 style='color:white'>Error..</h2>";
}
