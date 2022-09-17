<?php
session_start();
if (!(isset($_SESSION) && isset($_SESSION["who"]))) {
    header("Location:../../../");
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>
        All Chat | Edit Profile |Change Picture
    </title>
    <meta name="viewport" content="width=device-width,initial-scale=1.0" />
    <link rel="stylesheet" href="../../../Extra/styles/cssFiles/themes.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <style>
        body {
            background-color: rgb(247, 247, 247);
        }

        body,
        html {
            height: 100%;
        }
    </style>
    <script>
        function hideMainBoxBeforeLoading(box_id) {
            $("#" + box_id + "").css({
                'pointer-events': 'none',
                'user-select': 'none'
            });
            $("#" + box_id + "").animate({
                opacity: 0
            }, 300);
        }

        function showLoadingBox() {
            let temp = document.createElement('div');
            temp.setAttribute('id', 'loading_box_outer_id');
            temp.setAttribute('class', 'loading_box_outer');
            temp.innerHTML = '<div class=loading_bar></div><h3 class=loading_label>Loading</h3>';
            temp.style.opacity = '0';
            document.body.appendChild(temp);
            $('#loading_box_outer_id').animate({
                opacity: 1
            }, 1000);
        }

        function returnAfterLoading(box_id) {
            setTimeout(function() {
                $("#" + box_id + "").css({
                    'pointer-events': 'all'
                });
                $("#" + box_id + "").animate({
                    opacity: 1
                }, 1000);
                $('#loading_box_outer_id').animate({
                    opacity: 0
                }, 1000);
                $('#loading_box_outer_id').css({
                    'z-index': '-100'
                });
            }, 5000);
        }

        function startLoading(box_id) {
            hideMainBoxBeforeLoading(box_id);
            showLoadingBox();
            returnAfterLoading(box_id);
        }
    </script>
</head>

<body>
    <div id="whole_box">
        <div class="backButton">
            <a href="../"><img class="backbuttonlink" src="../../../Extra/styles/images/backButton.png" alt="Back Button" width="50px" height="50px" /></a>
        </div>
        <div class="outer">
            <div class="mainChangeBox">
                <table style="width:60%">
                    <caption style="color:red">
                        Image should be at most 1MB and should be of type PNG
                    </caption>
                    <thead>
                        <th>
                            <h2 id="message"></h2>
                        </th>
                    </thead>
                    <form action="" method="post" enctype="multipart/form-data">
                        <tbody class="changeBox">
                            <tr>
                                <td>
                                    <input type="file" name="image" class="inputfield" id="picField" required />
                                </td>

                            </tr>
                        </tbody>
                        <tfoot class="footOfPic" style="background:red">
                            <tr>
                                <td>
                                    <input type="submit" value="OK" class="buttontag" name="changeButton" />
                                </td>
                            </tr>
                        </tfoot>
                    </form>
                </table>
            </div>
        </div>
    </div>
</body>

</html>
<?php
if (isset($_POST) && isset($_POST["changeButton"])) {
    echo "<script>startLoading('whole_box');</script>";
    if ($_FILES['image']['name'] != "") {
        $name = $_FILES["image"]["name"];
        $type = substr($name, strlen($name) - 3, strlen($name) - 1);
        if ($type == "png") {
            define('MB', 1024000);
            $size = round((($_FILES["image"]["size"]) / MB), 0);
            if ($size <= 1) {
                require_once("../../../DB.php");
                $query = "UPDATE users SET picture='" . $_SESSION["who"] . "' WHERE BINARY username='" . $_SESSION["who"] . "'";
                if (mysqli_query($conn, $query)) {
                    echo
                    "
                        <script>
                            var errorfield=document.getElementById('message');
                            errorfield.style.color='green';
                            errorfield.innerHTML='Successfully Changed';
                        </script>
                        ";



                    $tmp_name = $_FILES["image"]["tmp_name"];
                    $path = "../../../Extra/styles/images/" . $_SESSION["who"] . ".png";
                    move_uploaded_file($tmp_name, $path);
                }
                mysqli_close($conn);
            } else {
                echo
                "
                        <script>
                            var errorfield=document.getElementById('message');
                            errorfield.style.color='#0162AF';
                            errorfield.innerHTML='Size is bigger than 1 MB';
                        </script>
                        ";
            }
        } else {
            echo
            "
                        <script>
                            var errorfield=document.getElementById('message');
                            errorfield.style.color='#0162AF';
                            errorfield.innerHTML='Type is not PNG';
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