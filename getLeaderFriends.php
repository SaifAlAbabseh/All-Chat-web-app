<?php
session_start();
if (isset($_SESSION) && isset($_SESSION["who"]) && isset($_REQUEST) && isset($_REQUEST["group_id"])) {
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
                    echo "
                                    
                                    <tr>
                                        <td><img src='../View_Image/?u=" . $row2[0] . "' width='50px' height='50px' style='border-radius:50%'/></td>
                                        <td><h2 style='color:white'>" . $row2[0] . "</h2></td>
                                        <td>
                                            <a style='border-color:green;' href='../../addMemberToGroup.php?group_id=" . $_REQUEST["group_id"] . "&user=" . $row2[0] . "' id='addlink' class='link'>Add</a>
                                        </td>
                                    </tr>


                                    ";
                }
            }
        }
    } else {
        echo "<tr><th><h2 style='color:white'>No friends to show..</h2></th></tr>";
    }
} else {
    echo "<tr><th><h2 style='color:white'>Error..</h2></th></tr>";
}
