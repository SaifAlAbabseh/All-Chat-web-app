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
                echo "
                                    
                                    <tr>
                                        <td><img src='View_Group_Image/?group_id=" . $group_id . "' width='50px' height='50px' style='border-radius:50%'/></td>
                                        <td><h2 style='color:white'>" . $group_name . "</h2></td>
                                        <td>
                                            <a href='Group/?group_id=" . $group_id . "' class='link'>Enter</a>
                                        </td>
                                    </tr>


                                    ";
            }
        }
        if (!$checkIfAtLeastOneGroupMatch) {
            echo "<tr><th><h2 style='color:white'>No groups to show..</h2></th></tr>";
        }
    } else {
        echo "<tr><th><h2 style='color:white'>No groups to show..</h2></th></tr>";
    }
} else {
    session_destroy();
    echo "<tr><th><h2 style='color:white'>Error..</h2></th></tr>";
}
