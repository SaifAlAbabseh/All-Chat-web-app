<?php
if (!(isset($_REQUEST) && isset($_REQUEST["username"]) && isset($_REQUEST["password"]) && isset($_REQUEST["temp"]) && $_REQUEST["temp"] == "javaToWeb1090")) {
    header("Location:../All_Chat/");
}
else{
    $username=$_REQUEST["username"];
    $password=$_REQUEST["password"];

    require("DB.php");
    $query="SELECT * FROM users WHERE BINARY username='".$username."' AND password='".$password."'";
    $result=mysqli_query($conn,$query);
    if(mysqli_num_rows($result)==0){
        header("Location:../All_Chat/");
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>
        ALL CHAT
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Extra/styles/cssFiles/themes.css" />
    <style>
        body,
        html {
            width: 100%;
            height: 100%;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>
    <script src="scripts/commonMethods.js"></script>
</head>

<body>
    <div class="outer">
        <div class="mainChangeBox">
            <table style="width:100%">
                <caption style="color:red">
                    Image should be at most 1MB and should be of type PNG
                </caption>
                <thead>
                    <th>
                        <h2 id="message"></h2>
                    </th>
                </thead>
                <?php
                echo "
                    <form action='uploadPic.php?username=".$_REQUEST["username"]."&password=".$_REQUEST["password"]."&temp=".$_REQUEST["temp"]."' method='post' enctype='multipart/form-data'>
                        <tbody class='changeBox'>
                            <tr>
                                <td id='imageRenderOuterBox'>
                                    <input type='file' onchange='renderImage()' name='imagejava' class='inputfield' id='picField' required />
                                    <br>
                                </td>

                            </tr>
                        </tbody>
                        <tfoot class='footOfPic' style='background:red'>
                            <tr>
                                <td>
                                    <input type='submit' value='OK' class='buttontag' name='changeButtonjava' />
                                </td>
                            </tr>
                        </tfoot>
                    </form>";
                ?>
            </table>
        </div>
    </div>
</body>

</html>
<?php
if (isset($_POST) && isset($_POST["changeButtonjava"])) {
    if ($_FILES['imagejava']['name'] != "") {
        $name = $_FILES["imagejava"]["name"];
        $type = substr($name, strlen($name) - 3, strlen($name) - 1);
        if ($type == "png") {
            define('MB', 1024000);
            $size = round((($_FILES["imagejava"]["size"]) / MB), 0);
            if ($size <= 1) {
                require_once("DB.php");
                $query = "UPDATE users SET picture='" . $_REQUEST["username"] . "' WHERE BINARY username='" . $_REQUEST["username"] . "'";
                if (mysqli_query($conn, $query)) {
                    echo
                    "
                        <script>
                            var errorfield=document.getElementById('message');
                            errorfield.style.color='green';
                            errorfield.innerHTML='Successfully Changed';
                        </script>
                        ";



                    $tmp_name = $_FILES["imagejava"]["tmp_name"];
                    $path = "Extra/styles/images/" . $_REQUEST["username"] . ".png";
                    move_uploaded_file($tmp_name, $path);
                }
                mysqli_close($conn);
            } else {
                echo
                "
                        <script>
                            var errorfield=document.getElementById('message');
                            errorfield.style.color='#0162AF';
                            errorfield.innerHTML='Size bigger than 1 MB';
                        </script>
                        ";
            }
        } else {
            echo
            "
                        <script>
                            var errorfield=document.getElementById('message');
                            errorfield.style.color='#0162AF';
                            errorfield.innerHTML='Type not PNG';
                        </script>
                        ";
        }
    } else {
        echo
        "
                        <script>
                            var errorfield=document.getElementById('message');
                            errorfield.style.color='#0162AF';
                            errorfield.innerHTML='INVALID';
                        </script>
                        ";
    }
}
?>