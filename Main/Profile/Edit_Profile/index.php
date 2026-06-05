<?php
session_start();
if (!(isset($_SESSION) && isset($_SESSION["who"]))) {
    header("Location:../../../");
}
require_once(dirname(__DIR__, 3) . '/common.php');
?>
<!DOCTYPE html>
<html>

<head>
    <link rel="icon" type="image/x-icon" href="../../../Extra/images/favicon.ico">
    <title>
        All Chat | Edit Profile | Change Picture
    </title>
    <meta name="viewport" content="width=device-width,initial-scale=1.0" />
    <link rel="stylesheet" href="<?= asset('../../../Extra/styles/cssFiles/themes.css') ?>" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        const currentTheme = localStorage.getItem('theme') || 'dark';
        if (currentTheme === 'light') {
            document.documentElement.classList.add('light-mode');
            document.addEventListener('DOMContentLoaded', () => {
                document.body.classList.add('light-mode');
            });
        }
    </script>
    <style>
        body,
        html {
            height: 100%;
        }
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }
        
        .modern-profile-box {
            background: rgba(43, 48, 58, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 15px;
            padding: 30px;
            width: 90%;
            max-width: 450px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            text-align: center;
            color: white;
            backdrop-filter: blur(10px);
        }
        body.light-mode .modern-profile-box {
            background: rgba(240, 242, 245, 0.95);
            color: #333;
            border-color: rgba(0,0,0,0.1);
        }
        
        .modern-profile-box h2.modal-title {
            color: white;
            font-size: 1.8rem;
            margin: 0 0 15px 0;
            border-bottom: 2px solid rgba(255,255,255,0.1);
            padding-bottom: 15px;
        }
        body.light-mode .modern-profile-box h2.modal-title {
            color: #333;
            border-bottom-color: rgba(0,0,0,0.1);
        }
        
        .modern-profile-box .instruction {
            color: #F76D57;
            font-size: 0.95rem;
            margin-bottom: 20px;
            font-weight: bold;
        }
        
        .modern-form-group {
            display: flex;
            flex-direction: column;
            gap: 15px;
            align-items: center;
            width: 100%;
        }
        
        .modern-input {
            width: 100%;
            box-sizing: border-box;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            padding: 12px 15px;
            color: white;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        body.light-mode .modern-input {
            background: rgba(255, 255, 255, 0.8);
            border: 1px solid #ccc;
            color: #333;
        }
        .modern-input:focus {
            outline: none;
            border-color: #F76D57;
            box-shadow: 0 0 10px rgba(247, 109, 87, 0.3);
        }
        
        .modern-submit-btn {
            background: linear-gradient(135deg, #0162AF 0%, #004d8c 100%);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 12px 0;
            font-size: 1.2rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 10px;
            box-shadow: 0 4px 15px rgba(1, 98, 175, 0.3);
        }
        .modern-submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(1, 98, 175, 0.5);
        }
        
        #imageRenderOuterBox img {
            max-width: 100%;
            border-radius: 10px;
            margin-top: 15px;
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
    <script src="<?= asset('../../../scripts/commonMethods.js') ?>"></script>
</head>

<body>
    <div id="whole_box" style="width: 100%; display: flex; justify-content: center; align-items: center;">
        <div style="position: absolute; top: 20px; left: 20px; z-index: 1000;">
            <a href="../" class="backButton" style="text-decoration:none; font-size:1.5rem; color: #F76D57; font-weight:bold; background:rgba(255,255,255,0.1); padding:10px 20px; border-radius:20px; backdrop-filter:blur(5px); transition:all 0.3s ease;">🔙 Back</a>
        </div>
        
        <div class="modern-profile-box">
            <h2 class="modal-title">Change Profile Picture</h2>
            <div class="instruction">
                Image should be at most 1MB and should be of type PNG.
            </div>
            <h3 id="message" style="margin: 0 0 15px 0;"></h3>
            
            <form action="" method="post" enctype="multipart/form-data">
                <div class="modern-form-group">
                    <input type="file" onchange="renderImage()" name="image" class="modern-input" id="picField" required />
                    
                    <div id="imageRenderOuterBox"></div>
                    
                    <button type="submit" name="changeButton" class="modern-submit-btn">Upload & Save</button>
                </div>
            </form>
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
                $query = "UPDATE users SET picture=? WHERE BINARY username=?";
                $stmt = mysqli_prepare($conn, $query);
                $pic = $_SESSION["who"];
                $who = $_SESSION["who"];
                mysqli_stmt_bind_param($stmt, "ss", $pic, $who);
                if (mysqli_stmt_execute($stmt)) {
                    echo
                    "
                        <script>
                            var errorfield=document.getElementById('message');
                            errorfield.style.color='green';
                            errorfield.innerHTML='Successfully Changed';
                        </script>
                        ";



                    $tmp_name = $_FILES["image"]["tmp_name"];
                    $path = "../../../Extra/styles/images/users_images/" . $_SESSION["who"] . ".png";
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