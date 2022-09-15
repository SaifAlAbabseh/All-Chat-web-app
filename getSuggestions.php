<?php
session_start();
if (isset($_REQUEST) && isset($_REQUEST["value"]) && isset($_REQUEST["exact"]) && isset($_SESSION) && isset($_SESSION["who"])) {
    require_once("DB.php");
    $value = $_REQUEST["value"];
    $if_exact = $_REQUEST["exact"];
    $query = "SELECT * FROM users WHERE username LIKE '%".$value."%'";
    if($if_exact == "1"){
        $query = "SELECT * FROM users WHERE BINARY username='".$value."' ";
    }
    $result = mysqli_query($conn, $query);
    if ($result) {
        if (mysqli_num_rows($result)) {
            $output = "";
            $counter = 1;
            while ($row = mysqli_fetch_row($result)) {
                $output.="<div onclick=fill_result_username('r".$counter."') class='sug_row'><img src='../View Image/?u=" . $row[0] . "' width='50px' height='50px' style='border-radius:50%'/><h3 id='r".$counter."' style='color:red'>" . $row[0] . "</h3></div>";
                $counter++;
            }
            echo $output;
        } else {
            echo "<h2 class='no_sug_label' id='no_sug_label_box'>No Suggestions.</h2>";
        }
    }
    else{
        echo "<h2 class='no_sug_label' style='color:red'>Error!</h2>";
    }
} else {
    header("Location:../All_Chat/");
}
