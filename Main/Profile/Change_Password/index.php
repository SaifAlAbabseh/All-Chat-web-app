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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Chat | Edit Profile | Change Password</title>
    <link rel="stylesheet" href="<?= asset('../../../Extra/styles/cssFiles/themes.css') ?>" />

    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
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
        <a href="../" class="backButton" style="text-decoration:none; font-size:1.5rem; color: #F76D57; font-weight:bold; background:rgba(255,255,255,0.1); padding:10px 20px; border-radius:20px; backdrop-filter:blur(5px); transition:all 0.3s ease;">🔙 Back</a>
    </div>
    <form action="" method="POST" class="changePasswordForm">
        <table>
            <tr>
                <td>
                    <input type="password" required name="currentPass" class="inputfield" placeholder="Current Password">
                </td>
            </tr>
            <tr>
                <td>
                    <input type="password" required name="newPass" class="inputfield" placeholder="New Password">
                    <input type="button" value=" i " class="i" id="passreq">
                </td>
            </tr>
            <tr>
                <td>
                    <input type="password" required name="confirmNewPass" class="inputfield" placeholder="Confirm New Password">
                </td>
            </tr>
            <tr>
                <td align="center">
                    <br><br>
                    <input type="submit" value="Change" class="buttontag" name="changePassButton">
                    <input type="reset" value="Clear" class="buttontag">
                </td>
            </tr>
        </table>
    </form>
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
                                echo "<script>alert('Successfully changed password :)');</script>";
                            } else {
                                echo "<script>alert('Invalid Error..');</script>";
                            }
                        } else {
                            echo "<script>alert('Invalid current password..');</script>";
                        }
                    mysqli_close($conn);
                } else {
                    echo "<script>alert('New password and confirmed one are not identical.. please try again');</script>";
                }
            } else {
                echo "<script>alert('New password does not follow the requirements..');</script>";
            }
        } else {
            echo "<script>alert('Invalid');</script>";
        }
    } else {
        echo "<script>alert('Invalid');</script>";
    }
}

?>