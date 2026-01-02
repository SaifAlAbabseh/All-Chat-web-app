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
            align-self: center;
            justify-content: center;
            background-color: rgb(247, 247, 247);
        }

        .backButton {
            position: absolute;
            left: 0;
            top: 0;
        }
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="<?= asset('../../../scripts/index.js') ?>"></script>
</head>

<body>
    <div class="backButton">
        <a href="../"><img class="backbuttonlink" src="../../../Extra/styles/images/backButton.png" alt="Back Button" width="50px" height="50px" /></a>
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
                    $checkCurrentPassQuery = "SELECT * FROM users WHERE username = '" . $_SESSION["who"] . "' AND password = '" . md5($currentPass) . "'";
                    $result = mysqli_query($conn, $checkCurrentPassQuery);
                    if ($result) {
                        if (mysqli_num_rows($result) > 0) {
                            $changePassQuery = "UPDATE users SET password='" . md5($newPass) . "' WHERE username='" . $_SESSION["who"] . "'";
                            $result = mysqli_query($conn, $changePassQuery);
                            if ($result) {
                                echo "<script>alert('Successfully changed password :)');</script>";
                            } else {
                                echo "<script>alert('Invalid Error..');</script>";
                            }
                        } else {
                            echo "<script>alert('Invalid current password..');</script>";
                        }
                    } else {
                        echo "<script>alert('Unknown Error..');</script>";
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