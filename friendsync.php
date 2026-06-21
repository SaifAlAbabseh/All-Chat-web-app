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
                                    <div class='friendRow' data-username='" . htmlspecialchars($row2[0], ENT_QUOTES) . "' title='" . htmlspecialchars($row2[0], ENT_QUOTES) . "'>
                                        <div class='unread-bell' style='display:none;'>
                                            <svg viewBox='0 0 24 24' fill='none' xmlns='http://www.w3.org/2000/svg' style='width: 16px; height: 16px; margin: auto; display: block;'>
                                                <path d='M12 22C13.1 22 14 21.1 14 20H10C10 21.1 10.89 22 12 22ZM18 16V11C18 7.93 16.36 5.36 13.5 4.68V4C13.5 3.17 12.83 2.5 12 2.5C11.17 2.5 10.5 3.17 10.5 4V4.68C7.63 5.36 6 7.92 6 11V16L4 18V19H20V18L18 16Z' fill='currentColor'/>
                                            </svg>
                                        </div>
                                        <div style='display: flex; flex-direction: column; align-items: center; justify-content: center; width: 100%;'>
                                            <img src='View_Image/?u=" . urlencode($row2[0]) . "' width='60px' height='60px' style='border-radius:50%; object-fit: cover; border:solid 3px " . (($ava == "0") ? "#ef4444" : "#10b981") . "'/>
                                        </div>
                                        <h2 class='friendUsername'>" . htmlspecialchars($row2[0]) . "</h2> 
                                        <div class='bubble-btn-container'>
                                            <a href='Chat/?with=" . urlencode($row2[0]) . "' class='bubble-btn chat' title='Chat' onclick='clearUnreadFriend(\"" . htmlspecialchars($row2[0], ENT_QUOTES) . "\")'>&#128172;</a>
                                            <button onclick='openDeleteModal(\"".htmlspecialchars($row2[0], ENT_QUOTES)."\")' class='bubble-btn delete' title='Delete'>&#128465;</button>
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
