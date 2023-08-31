<?php
session_start();
if (isset($_REQUEST) && isset($_REQUEST["value"]) && isset($_REQUEST["exact"]) && isset($_SESSION) && isset($_SESSION["who"])) {
    if (checkInput($_REQUEST["value"])) {
        require_once("DB.php");
        $value = $_REQUEST["value"];
        $if_exact = $_REQUEST["exact"];
        $query = "SELECT * FROM users WHERE username LIKE '%" . $value . "%' AND username NOT LIKE '%" . $_SESSION["who"] . "%'";
        $result = mysqli_query($conn, $query);
        if ($result) {
            if (mysqli_num_rows($result)) {
                $output = "";
                $counter = 1;
                while ($row = mysqli_fetch_row($result)) {
                    $output .= "<div onclick=fill_result_username('r" . $counter . "') class='sug_row'><img src='../View Image/?u=" . $row[0] . "' width='50px' height='50px' style='border-radius:50%'/><h3 id='r" . $counter . "' style='color:red'>" . $row[0] . "</h3></div>";
                    $counter++;
                }
                echo $output;
            } else {
                echo "<h2 class='no_sug_label' id='no_sug_label_box'>No Suggestions.</h2>";
            }
        } else {
            echo "<h2 class='no_sug_label' style='color:red'>Error!</h2>";
        }
    }
    else {
        echo "<h2 class='no_sug_label' style='color:red'>Invalid Characters</h2>";
    }
} else {
    echo "<h2 class='no_sug_label' style='color:red'>Error!</h2>";
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

?>