<?php
session_start();
if (isset($_SESSION) && isset($_SESSION["who"])) {
    require_once("DB.php");
    $query = "SELECT group_id, group_name, group_image FROM all_chat_groups";
    $result = mysqli_query($conn, $query);
    if ($result) {
        if (mysqli_num_rows($result)) {
            $checkIfAtLeastOneGroupMatch = false;
            while ($row = mysqli_fetch_row($result)) {
                $group_id = $row[0];
                $group_name = $row[1];
                $group_image = $row[2];
                $group_table_name = "g" . $group_id . "_users";
                $get_group_query = "SELECT user_type FROM $group_table_name WHERE BINARY username='" . $_SESSION["who"] . "'";
                $groups_query_result = mysqli_query($conn, $get_group_query);
                if (mysqli_num_rows($groups_query_result)) {
                    $checkIfAtLeastOneGroupMatch = true;
                    echo "
                                    
                                    <tr>
                                        <td><img src='View Group Image/?group_id=" . $group_id . "' width='50px' height='50px' style='border-radius:50%'/></td>
                                        <td><h2 style='color:white'>" . $group_name . "</h2></td>
                                        <td>
                                            <a href='Group/?group_id=" . $group_id . "' class='link'>Enter</a>
                                        </td>
                                    </tr>


                                    ";
                }
            }
            if(!$checkIfAtLeastOneGroupMatch){
                echo "<tr><th><h2 style='color:white'>No groups to show..</h2></th></tr>";
            }
        } else {
            echo "<tr><th><h2 style='color:white'>No groups to show..</h2></th></tr>";
        }
    } else {
        session_destroy();
        echo "<tr><th><h2 style='color:white'>Error..</h2></th></tr>";
    }
} else {
    echo "<tr><th><h2 style='color:white'>Error..</h2></th></tr>";
}