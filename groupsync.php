<?php
session_start();
if (isset($_SESSION) && isset($_SESSION["who"])) {
    require_once("DB.php");
    $query = "SELECT group_id, group_name, group_image FROM all_chat_groups";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (mysqli_num_rows($result)) {
        $checkIfAtLeastOneGroupMatch = false;
        while ($row = mysqli_fetch_row($result)) {
            $group_id = $row[0];
            $group_name = $row[1];
            $group_image = $row[2];
            $group_table_name = "g" . $group_id . "_users";
            $get_group_query = "SELECT user_type FROM $group_table_name WHERE BINARY username=?";
            $stmt2 = mysqli_prepare($conn, $get_group_query);
            $who = $_SESSION["who"];
            mysqli_stmt_bind_param($stmt2, "s", $who);
            mysqli_stmt_execute($stmt2);
            $groups_query_result = mysqli_stmt_get_result($stmt2);
            if (mysqli_num_rows($groups_query_result)) {
                $checkIfAtLeastOneGroupMatch = true;
                $user_row = mysqli_fetch_row($groups_query_result);
                $user_type = $user_row[0];
                $admin_badge = "";
                if (strtolower($user_type) == "leader") {
                    $admin_badge = "<div style='margin-top: 5px; padding: 2px 6px; font-size: 0.65rem; font-weight: 800; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border-radius: 8px; box-shadow: 0 2px 5px rgba(16, 185, 129, 0.4); text-transform: uppercase; letter-spacing: 0.5px;'>Admin</div>";
                }
                echo "
                                    <div class='groupRow' style='align-items: center;'>
                                        <div style='display: flex; flex-direction: column; align-items: center; justify-content: center; min-width: 60px;'>
                                            <img src='View_Group_Image/?group_id=" . $group_id . "' width='50px' height='50px' style='border-radius:50%'/>
                                            " . $admin_badge . "
                                        </div>
                                        <h2 class='friendUsername'>" . htmlspecialchars($group_name) . "</h2> 
                                        <a href='Group/?group_id=" . $group_id . "' class='link'>Enter</a>
                                    </div>


                                    ";
            }
        }
        if (!$checkIfAtLeastOneGroupMatch) {
            echo "<h2 class='empty-state-msg'>No groups to show..</h2>";
        }
    } else {
        echo "<h2 class='empty-state-msg'>No groups to show..</h2>";
    }
} else {
    session_destroy();
    echo "<h2 class='empty-state-msg'>Error..</h2>";
}
