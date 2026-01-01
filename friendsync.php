<?php
session_start();
if (isset($_SESSION) && isset($_SESSION["who"])) {
    require_once("DB.php");
    $query = "SELECT * FROM friends WHERE BINARY user1='" . $_SESSION["who"] . "' OR BINARY user2='" . $_SESSION["who"] . "'";
    $result = mysqli_query($conn, $query);
    if ($result) {
        if (mysqli_num_rows($result)) {
            while ($row = mysqli_fetch_row($result)) {
                $user1 = $row[0];
                $user2 = $row[1];
                $get;
                if ($user1 == $_SESSION["who"]) {
                    $get = "SELECT * FROM users WHERE BINARY username='" . $user2 . "'";
                } else if ($user2 == $_SESSION["who"]) {
                    $get = "SELECT * FROM users WHERE BINARY username='" . $user1 . "'";
                }
                $result2 = mysqli_query($conn, $get);
                if (mysqli_num_rows($result2)) {
                    while ($row2 = mysqli_fetch_row($result2)) {
                        $ava;
                        if ($row2[3] == "0") {
                            $ava = "<div style='border-radius:50%;background-color:red;width:10px;height:10px'></div>";
                        } else if ($row2[3] == "1") {
                            $ava = "<div style='border-radius:50%;background-color:lightgreen;width:10px;height:10px'></div>";
                        }
                        echo "
                                    
                                    <tr>
                                        <td><img src='View_Image/?u=" . $row2[0] . "' width='50px' height='50px' style='border-radius:50%'/> <sup>" . $ava . "</sup></td>
                                        <td><h2 style='color:white'>" . $row2[0] . "</h2></td>
                                        <td>
                                            <a href='Chat/?with=" . $row2[0] . "' class='link'>Chat</a>
                                        </td>
                                        <td>
                                            <a href='Delete_Friend/?name=" . $row2[0] . "' class='link'>Delete</a>
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
} else {
    session_destroy();
    echo "<tr><th><h2 style='color:white'>Error..</h2></th></tr>";
}
?>