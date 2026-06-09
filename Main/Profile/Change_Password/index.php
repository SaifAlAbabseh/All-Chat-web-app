<!DOCTYPE html>
<?php
session_start();
if (!(isset($_SESSION) && isset($_SESSION["who"]))) {
    header("Location:../../../");
}
require_once(dirname(__DIR__, 3) . '/common.php');
?>

<html lang="en">

<head>
    <link rel="icon" type="image/x-icon" href="../../../Extra/images/favicon.ico">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Chat | Edit Profile | Change Password</title>
    <link rel="stylesheet" href="<?= asset('../../../Extra/styles/cssFiles/themes.css') ?>" />
    <script src="<?= asset('../../../scripts/commonMethods.js') ?>"></script>

    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        .modern-change-pass-box {
            background: rgba(43, 48, 58, 0.8);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
            text-align: center;
            box-sizing: border-box;
        }
        .modern-title {
            font-size: 1.8rem;
            color: #F76D57;
            margin: 0 0 25px 0;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        .modern-input-group {
            margin-bottom: 20px;
            text-align: left;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .modern-input-group label {
            font-weight: 600;
            font-size: 0.95rem;
            opacity: 0.9;
        }
        .modern-input {
            width: 100%;
            padding: 12px 15px;
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: white;
            font-size: 1rem;
            box-sizing: border-box;
            transition: all 0.3s ease;
        }
        .modern-input:focus {
            outline: none;
            border-color: #F76D57;
            background: rgba(0, 0, 0, 0.3);
            box-shadow: 0 0 0 3px rgba(247, 109, 87, 0.2);
        }
        .modern-btn {
            background: linear-gradient(135deg, #F76D57 0%, #ff523a 100%);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 12px 25px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(247, 109, 87, 0.4);
            width: 100%;
            box-sizing: border-box;
        }
        .modern-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(247, 109, 87, 0.6);
        }
        .modern-btn-clear {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            box-shadow: none;
            margin-top: 15px;
        }
        .modern-btn-clear:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        
        /* Light Mode Support */
        body.light-mode .modern-change-pass-box {
            background: rgba(255, 255, 255, 0.85);
            border-color: rgba(0, 0, 0, 0.1);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.1);
        }
        body.light-mode .modern-input {
            background: rgba(255, 255, 255, 0.6);
            border-color: rgba(0, 0, 0, 0.2);
            color: #2B303A;
        }
        body.light-mode .modern-input:focus {
            background: #fff;
            border-color: #034780;
            box-shadow: 0 0 0 3px rgba(3, 71, 128, 0.2);
        }
        body.light-mode .modern-btn-clear {
            background: rgba(0, 0, 0, 0.05);
            border-color: rgba(0, 0, 0, 0.1);
            color: #2B303A;
        }
        body.light-mode .modern-btn-clear:hover {
            background: rgba(0, 0, 0, 0.1);
        }
        body.light-mode .backButton {
            color: #034780 !important;
            border-color: rgba(0,0,0,0.1) !important;
        }
        body.light-mode #passreq {
            border-color: rgba(0,0,0,0.2) !important;
            color: #034780 !important;
        }
    </style>
    <script>
        const currentTheme = localStorage.getItem('theme') || 'dark';
        if (currentTheme === 'light') {
            document.documentElement.classList.add('light-mode');
            document.addEventListener('DOMContentLoaded', () => {
                document.body.classList.add('light-mode');
            });
        }
    </script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="<?= asset('../../../scripts/index.js') ?>"></script>
</head>

<body>
    <div style="position: absolute; top: 20px; left: 20px; z-index: 1000;">
        <a href="../" class="backButton" style="text-decoration:none; font-size:1.2rem; color: #F76D57; font-weight:bold; background:rgba(255,255,255,0.1); padding:10px 20px; border-radius:20px; backdrop-filter:blur(5px); border: 1px solid rgba(255,255,255,0.1); transition:all 0.3s ease; box-shadow: 0 4px 10px rgba(0,0,0,0.2);">🔙 Back</a>
    </div>
    
    <div class="modern-change-pass-box">
        <h2 class="modern-title">Change Password</h2>
        <form action="" method="POST">
            <div class="modern-input-group">
                <label>Current Password</label>
                <input type="password" required name="currentPass" class="modern-input" placeholder="Enter current password...">
            </div>
            
            <div class="modern-input-group">
                <label>New Password</label>
                <div style="display:flex; gap: 10px;">
                    <input type="password" required name="newPass" class="modern-input" placeholder="Enter new password...">
                    <div class="i" id="passreq" style="display:flex; align-items:center; justify-content:center; width: 45px; border-radius: 12px; cursor: pointer; border: 1px solid rgba(255,255,255,0.2); background: rgba(255,255,255,0.05); transition: all 0.3s ease;">i</div>
                </div>
            </div>
            
            <div class="modern-input-group">
                <label>Confirm New Password</label>
                <input type="password" required name="confirmNewPass" class="modern-input" placeholder="Confirm new password...">
            </div>
            
            <div style="margin-top: 30px;">
                <button type="submit" class="modern-btn" name="changePassButton">Change Password</button>
                <button type="reset" class="modern-btn modern-btn-clear">Clear Form</button>
            </div>
        </form>
    </div>
    <div class="outer_reqs_box" onclick="hide()">
        <div class="reqs" id="username_reqs">
            <p>
                Username shoud not contain spaces , symbols and it should be at least 6 characters in length and at most 12.
            </p>
        </div>
        <div class="reqs" id="password_reqs">
            <p>
                Password should not contain spaces , and it should be at least 8 characters in length and at most 16.
            </p>
        </div>
    </div>
</body>

</html>

<?php

if (isset($_POST) && isset($_POST["changePassButton"])) {
    extract($_POST);
    if (isset($currentPass) && isset($newPass) && isset($confirmNewPass)) {
        if ($currentPass != "" && $newPass != "" && $confirmNewPass != "") {
            if (!(strpos($newPass, " ") || !(strlen($newPass) >= 8 && strlen($newPass) <= 16))) {
                if ($newPass == $confirmNewPass) {
                    require_once("../../../DB.php");
                    $checkCurrentPassQuery = "SELECT password FROM users WHERE username = ?";
                    $stmt = mysqli_prepare($conn, $checkCurrentPassQuery);
                    $who = $_SESSION["who"];
                    mysqli_stmt_bind_param($stmt, "s", $who);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    
                    $isPassValid = false;
                    if (mysqli_num_rows($result) > 0) {
                        $row = mysqli_fetch_assoc($result);
                        $db_pass = $row['password'];
                        if (password_verify($currentPass, $db_pass) || md5($currentPass) === $db_pass) {
                            $isPassValid = true;
                        }
                    }
                    
                    if ($isPassValid) {
                            $changePassQuery = "UPDATE users SET password=? WHERE username=?";
                            $stmt2 = mysqli_prepare($conn, $changePassQuery);
                            $pwd = password_hash($newPass, PASSWORD_BCRYPT);
                            mysqli_stmt_bind_param($stmt2, "ss", $pwd, $who);
                            if (mysqli_stmt_execute($stmt2)) {
                                echo "<script>window.customAlert('Successfully changed password :)');</script>";
                            } else {
                                echo "<script>window.customAlert('Invalid Error..');</script>";
                            }
                        } else {
                            echo "<script>window.customAlert('Invalid current password..');</script>";
                        }
                    mysqli_close($conn);
                } else {
                    echo "<script>window.customAlert('New password and confirmed one are not identical.. please try again');</script>";
                }
            } else {
                echo "<script>window.customAlert('New password does not follow the requirements..');</script>";
            }
        } else {
            echo "<script>window.customAlert('Invalid');</script>";
        }
    } else {
        echo "<script>window.customAlert('Invalid');</script>";
    }
}

?>