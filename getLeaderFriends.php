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
                                    <div class='friendRow' title= '" . $row2[0] . "'>
                                        <div style='display: flex; flex-direction: column; align-items: center; justify-content: center; width: 100%;'>
                                            <img src='../View_Image/?u=" . $row2[0] . "' width='60px' height='60px' style='border-radius:50%; object-fit: cover; border:none;'/>
                                        </div>
                                        <h2 class='friendUsername'>" . $row2[0] . "</h2> 
                                        <div class='bubble-btn-container'>
                                            <button onclick='addMemberToGroup(\"" . $row2[0] . "\")' class='bubble-btn chat' title='Add' style='background: linear-gradient(135deg, #10b981, #059669); font-size: 20px;'>➕</button>
                                        </div>
                                    </div>
                            ";
                }
            }
        }
    } else {
        echo "<div style='text-align:center; padding: 20px;'><h2 style='margin:0; color: #888;'>Start adding people..</h2></div>";
    }
} else {
    echo "<div style='text-align:center; padding: 20px;'><h2 style='margin:0; color: #888;'>Error..</h2></div>";
}
