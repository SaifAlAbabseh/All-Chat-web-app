<?php 
if(isset($_REQUEST) && isset($_REQUEST["check"]) && $_REQUEST["check"]=="fromMobile1090" && isset($_REQUEST["username"]) && isset($_REQUEST["password"]) && isset($_REQUEST["tableName"])){
    $tablename=$_REQUEST["tableName"];

    require_once("../DB.php");

    $query="SELECT * FROM ".$tablename."";
    $result=mysqli_query($conn,$query);
    if($result){
        if(mysqli_num_rows($result)){
               $res=Array();
               
               while($row=mysqli_fetch_row($result)){
                    $json=Array("fromwho" => $row[0], "message" => $row[1]);
                    array_push($res, $json);
               }
               echo json_encode($res);
        }
        else{
            echo "No Messages";
        }
    }
    else{
        echo "Unknown Error";
    }
}
else{
    header("../Location:index.php");
}
