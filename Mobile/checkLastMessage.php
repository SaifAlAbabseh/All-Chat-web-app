<?php 
if(isset($_REQUEST) && isset($_REQUEST["check"]) && $_REQUEST["check"]=="fromMobile1090" && isset($_REQUEST["username"]) && isset($_REQUEST["password"]) && isset($_REQUEST["tableName"]) && isset($_REQUEST["lastIndex"]) ){
    $tablename=$_REQUEST["tableName"];
    $lastIndex=$_REQUEST["lastIndex"];

    require_once("../DB.php");

    $query="SELECT * FROM ".$tablename."";
    $result=mysqli_query($conn,$query);
    if($result){
        if(mysqli_num_rows($result)){
            $actualLast=mysqli_num_rows($result);
            if($actualLast==$lastIndex){
                echo "no";
            }
            else{
                $row;
                $tempIndex=1;
                while($tempIndex<=$actualLast){
                    $row=mysqli_fetch_row($result);
                    $tempIndex++;
                }
                $fromwho=$row[0];
                $message=$row[1]; 

                $res=Array("fromwho" => $fromwho, "message" => $message);
                echo json_encode($res);
            }
        }
        else{
            echo "No Messages";
        }
    }
    else{
        echo "Unknown Error";
    }
    mysqli_close($conn);
}
else{
    header("../Location:index.php");
}
?>