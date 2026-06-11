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
            $get = "SELECT * FROM users WHERE BINARY username=?";
            $username_to_get = ($user1 == $_SESSION["who"]) ? $user2 : $user1;
            $stmt2 = mysqli_prepare($conn, $get);
            mysqli_stmt_bind_param($stmt2, "s", $username_to_get);
            mysqli_stmt_execute($stmt2);
            $result2 = mysqli_stmt_get_result($stmt2);
            if (mysqli_num_rows($result2)) {
                while ($row2 = mysqli_fetch_row($result2)) {
                    $ava = $row2[3];
                    echo "
                                    <div class='friendRow' title= '" . $row2[0] . "'>
                                        <div style='display: flex; flex-direction: column; align-items: center; justify-content: center; width: 100%;'>
                                            <img src='View_Image/?u=" . $row2[0] . "' width='60px' height='60px' style='border-radius:50%; object-fit: cover; border:solid 3px " . (($ava == "0") ? "#ef4444" : "#10b981") . "'/>
                                        </div>
                                        <h2 class='friendUsername'>" . $row2[0] . "</h2> 
                                        <div class='bubble-btn-container'>
                                            <a href='Chat/?with=" . $row2[0] . "' class='bubble-btn chat' title='Chat'>&#128172;</a>
                                            <button onclick='openDeleteModal(\"".$row2[0]."\")' class='bubble-btn delete' title='Delete'>&#128465;</button>
                                        </div>
                                    </div>
                            ";
                }
            }
        }
    } else {
        echo "<h2 class='empty-state-msg'>Start adding people..</h2>";
    }
} else {
    session_destroy();
    echo "<h2 class='empty-state-msg'>Error..</h2>";
}
