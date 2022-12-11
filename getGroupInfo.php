<?php
session_start();
if (!(isset($_SESSION) && isset($_SESSION["who"]) && isset($_REQUEST) && isset($_REQUEST["group_id"]))) {
    echo "<tr><td><h1 style='color:white'>Error..</h1></td></tr>";
} else {
    $group_id = $_REQUEST["group_id"];
    $username = $_SESSION["who"];

    require_once("DB.php");
    $group_table_name = "g" . $group_id . "_users";
    $check_query = "SELECT user_type FROM $group_table_name WHERE BINARY username='" . $username . "'";
    $check_result = mysqli_query($conn, $check_query);
    if ($check_query) {
        if (mysqli_num_rows($check_result)) {
            $query = "SELECT * FROM $group_table_name WHERE NOT BINARY username='" . $username . "'";
            $result = mysqli_query($conn, $query);
            if ($result) {
                if (mysqli_num_rows($result)) {
                    $temp_row = mysqli_fetch_row($check_result);
                    while ($row = mysqli_fetch_row($result)) {
                        if ($temp_row[0] == "leader") {
                            echo "
                                        
                                        <tr>
                                            <td><img src='../View Image/?u=" . $row[0] . "' width='50px' height='50px' style='border-radius:50%'/></td>
                                            <td><h2 style='color:white'>" . $row[0] . "</h2></td>
                                            <td><a style='color:blue;border:blue 2px solid;' class='link' href='../../kickMember.php?group_id=" . $group_id . "&member=" . $row[0] . "' id='kicklink'>Kick</a></td>
                                        </tr>
    
    
                                        ";
                        } else {
                            if($row[1]=="leader"){
                                echo "
                                        
                                        <tr>
                                            <td><img src='../View Image/?u=" . $row[0] . "' width='50px' height='50px' style='border-radius:50%'/></td>
                                            <td><h2 style='color:white'>" . $row[0] . "<sup style='font-style:italic;color:green;'>[Admin]</sup></h2></td>
                                        </tr>
    
    
                                        ";
                            }
                            else{
                                echo "
                                        
                                        <tr>
                                            <td><img src='../View Image/?u=" . $row[0] . "' width='50px' height='50px' style='border-radius:50%'/></td>
                                            <td><h2 style='color:white'>" . $row[0] . "</h2></td>
                                        </tr>
    
    
                                        ";
                            }
                        }
                    }
                } else {
                    echo "<tr><td><h1 style='color:white'>There are no members yet..</h1></td></tr>";
                }
            } else {
                echo "<tr><td><h1 style='color:white'>Error..</h1></td></tr>";
            }
        } else {
            echo "<tr><td><h1 style='color:white'>Youre not in this group..</h1></td></tr>";
        }
    } else {
        echo "<tr><td><h1 style='color:white'>Error..</h1></td></tr>";
    }
}
