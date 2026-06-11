<?php
session_start();
if (isset($_REQUEST) && isset($_REQUEST["value"]) && isset($_REQUEST["exact"]) && isset($_SESSION) && isset($_SESSION["who"])) {
    if (checkInput($_REQUEST["value"])) {
        require_once("DB.php");
        $value = $_REQUEST["value"];
        $if_exact = $_REQUEST["exact"];
        $query = "SELECT * FROM users WHERE username LIKE ? AND username NOT LIKE ?";
        $stmt = mysqli_prepare($conn, $query);
        $value_like = '%' . $value . '%';
        $not_like = '%' . $_SESSION["who"] . '%';
        mysqli_stmt_bind_param($stmt, "ss", $value_like, $not_like);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($result)) {
            $output = "";
            $counter = 1;
            while ($row = mysqli_fetch_row($result)) {
                $output .= "<div onclick=fill_result_username('r" . $counter . "') class='friendRow' style='cursor: pointer; width: 110px; height: auto; padding: 15px 10px; gap: 8px;'><img src='../View_Image/?u=" . $row[0] . "' width='60px' height='60px' style='border-radius:50%; object-fit: cover;'/><h2 id='r" . $counter . "' class='friendUsername' style='font-size: 0.95rem; margin: 0;'>" . $row[0] . "</h2></div>";
                $counter++;
            }
            echo $output;
        } else {
            echo "<h2 class='no_sug_label' id='no_sug_label_box'>No Suggestions.</h2>";
        }
    } else {
        echo "<h2 class='no_sug_label' style='color:red'>Error!</h2>";
    }
} else {
    echo "<h2 class='no_sug_label' style='color:red'>Invalid Characters</h2>";
}


function checkInput($un)
{
    $everythingIsGood = true;
    for ($i = 0; $i < strlen($un); $i++) {
        if (!((ord(substr($un, $i, 1)) >= 48 && ord(substr($un, $i, 1)) <= 57) || (ord(substr($un, $i, 1)) >= 65 && ord(substr($un, $i, 1)) <= 90) || (ord(substr($un, $i, 1)) >= 97 && ord(substr($un, $i, 1)) <= 122))) {
            $everythingIsGood = false;
            break;
        }
    }
    return $everythingIsGood;
}
