<?php
session_start();
if (!(isset($_SESSION) && isset($_SESSION["who"]) && isset($_REQUEST) && isset($_REQUEST["group_id"]))) {
    echo "<tr><td><h1 style='margin:0'>Error..</h1></td></tr>";
} else {
    $group_id = $_REQUEST["group_id"];
    $username = $_SESSION["who"];

    require_once("DB.php");
    // Fetch original creator
    $creator_query = "SELECT leader_username FROM all_chat_groups WHERE group_id=?";
    $c_stmt = mysqli_prepare($conn, $creator_query);
    mysqli_stmt_bind_param($c_stmt, "s", $group_id);
    mysqli_stmt_execute($c_stmt);
    $c_result = mysqli_stmt_get_result($c_stmt);
    $creator_username = "";
    if ($c_result && mysqli_num_rows($c_result)) {
        $creator_username = mysqli_fetch_row($c_result)[0];
    }

    $group_table_name = "g" . $group_id . "_users";
    $check_query = "SELECT user_type FROM $group_table_name WHERE BINARY username=?";
    $stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $check_result = mysqli_stmt_get_result($stmt);
    if ($check_result) {
        if (mysqli_num_rows($check_result)) {
            $query = "SELECT * FROM $group_table_name WHERE NOT BINARY username=?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if (mysqli_num_rows($result)) {
                    $temp_row = mysqli_fetch_row($check_result);
                    while ($row = mysqli_fetch_row($result)) {
                        $isOtherLeader = ($row[1] == "leader");
                        $isOtherCreator = ($row[0] == $creator_username);
                        if ($temp_row[0] == "leader") {
                            $demoteBtn = "";
                            $kickBtn = "";
                            
                            if (!$isOtherCreator) {
                                if ($isOtherLeader) {
                                    if ($username == $creator_username) {
                                        // Only creator can demote admins
                                        $demoteBtn = "<button onclick='demoteMember(\"" . $row[0] . "\")' class='bubble-btn' title='Demote' style='background: linear-gradient(135deg, #f59e0b, #d97706); font-size:16px;'>⬇️</button>";
                                    }
                                }
                                $kickBtn = "<button onclick='kickUser(\"" . $row[0] . "\")' class='bubble-btn delete' title='Kick'>🗑️</button>";
                            }
                            
                                $promoteBtn = (!$isOtherLeader && !$isOtherCreator) ? "<button onclick='promoteMember(\"" . $row[0] . "\")' class='bubble-btn chat' title='Promote' style='background: linear-gradient(135deg, #3b82f6, #2563eb); font-size:16px;'>⬆️</button>" : "";
                            
                            if ($isOtherCreator) {
                                $badge = "<div style='background: #F76D57; color: white; font-size: 10px; font-weight: bold; padding: 2px 8px; border-radius: 10px; margin-top: -10px; z-index: 2;'>CREATOR</div>";
                                $borderStyle = "border:solid 3px #F76D57;";
                            } else if ($isOtherLeader) {
                                $badge = "<div style='background: #10b981; color: white; font-size: 10px; font-weight: bold; padding: 2px 8px; border-radius: 10px; margin-top: -10px; z-index: 2;'>ADMIN</div>";
                                $borderStyle = "border:solid 3px #10b981;";
                            } else {
                                $badge = "";
                                $borderStyle = "border:none;";
                            }

                            echo "
                                    <div class='friendRow' title= '" . $row[0] . "'>
                                        <div style='display: flex; flex-direction: column; align-items: center; justify-content: center; width: 100%;'>
                                            <img src='../View_Image/?u=" . $row[0] . "' width='60px' height='60px' style='border-radius:50%; object-fit: cover; " . $borderStyle . "'/>
                                            " . $badge . "
                                        </div>
                                        <h2 class='friendUsername'>" . $row[0] . "</h2> 
                                        <div class='bubble-btn-container'>
                                            " . $promoteBtn . $demoteBtn . $kickBtn . "
                                        </div>
                                    </div>
                                    ";
                        } else {
                            if($row[1]=="leader"){
                                if ($isOtherCreator) {
                                    $badge = "<div style='background: #F76D57; color: white; font-size: 10px; font-weight: bold; padding: 2px 8px; border-radius: 10px; margin-top: -10px; z-index: 2;'>CREATOR</div>";
                                    $borderStyle = "border:solid 3px #F76D57;";
                                } else {
                                    $badge = "<div style='background: #10b981; color: white; font-size: 10px; font-weight: bold; padding: 2px 8px; border-radius: 10px; margin-top: -10px; z-index: 2;'>ADMIN</div>";
                                    $borderStyle = "border:solid 3px #10b981;";
                                }
                                echo "
                                    <div class='friendRow' title= '" . $row[0] . "'>
                                        <div style='display: flex; flex-direction: column; align-items: center; justify-content: center; width: 100%;'>
                                            <img src='../View_Image/?u=" . $row[0] . "' width='60px' height='60px' style='border-radius:50%; object-fit: cover; " . $borderStyle . "'/>
                                            " . $badge . "
                                        </div>
                                        <h2 class='friendUsername'>" . $row[0] . "</h2> 
                                        <div class='bubble-btn-container'>
                                        </div>
                                    </div>
                                        ";
                            }
                            else{
                                echo "
                                    <div class='friendRow' title= '" . $row[0] . "'>
                                        <div style='display: flex; flex-direction: column; align-items: center; justify-content: center; width: 100%;'>
                                            <img src='../View_Image/?u=" . $row[0] . "' width='60px' height='60px' style='border-radius:50%; object-fit: cover; border:none;'/>
                                        </div>
                                        <h2 class='friendUsername'>" . $row[0] . "</h2> 
                                        <div class='bubble-btn-container'>
                                        </div>
                                    </div>
                                        ";
                            }
                        }
                    }
                } else {
                    echo "<tr><td><h1 style='margin:0'>There are no members yet..</h1></td></tr>";
                }
        } else {
            echo "<tr><td><h1 style='margin:0'>Youre not in this group..</h1></td></tr>";
        }
    } else {
        echo "<tr><td><h1 style='margin:0'>Error..</h1></td></tr>";
    }
}
