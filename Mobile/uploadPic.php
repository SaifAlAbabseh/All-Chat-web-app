<?php

require_once("../common.php");
require_once("../DB.php");

if (!(isset($_REQUEST) && isset($_REQUEST["username"]) && isset($_REQUEST["password"]) && isset($_REQUEST["temp"]) && $_REQUEST["temp"] == "javaToWeb1090")) {
    header("Location:../index.php");
}
else{
    $username=$_REQUEST["username"];
    $password=$_REQUEST["password"];

    $query="SELECT * FROM users WHERE BINARY username=? AND password=?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ss", $username, $password);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if(mysqli_num_rows($result)==0){
        header("Location:../index.php");
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
    <link rel="stylesheet" href="<?= asset('../Extra/styles/cssFiles/themes.css') ?>" />
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
    <script src="<?= asset('../scripts/commonMethods.js') ?>"></script>
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
                $query = "UPDATE users SET picture=? WHERE BINARY username=?";
                $stmt = mysqli_prepare($conn, $query);
                $pic_name = $_REQUEST["username"];
                mysqli_stmt_bind_param($stmt, "ss", $pic_name, $username);
                if (mysqli_stmt_execute($stmt)) {
                    echo
                    "
                        <script>
                            var errorfield=document.getElementById('message');
                            errorfield.style.color='green';
                            errorfield.innerHTML='Successfully Changed';
                        </script>
                        ";



                    $tmp_name = $_FILES["imagejava"]["tmp_name"];
                    $path = "Extra/styles/images/users_images/" . $_REQUEST["username"] . ".png";
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