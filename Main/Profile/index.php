<?php
session_start();
if (!(isset($_SESSION) && isset($_SESSION["who"]))) {
    header("Location:../../");
}
require_once(dirname(__DIR__, 2) . '/common.php');
?>
<!DOCTYPE html>
<html>

<head>
    <title>
        All Chat | Edit Profile
    </title>
    <meta name="viewport" content="width=device-width,initial-scale=1.0" />
    <link rel="stylesheet" href="<?= asset('../../Extra/styles/cssFiles/themes.css') ?>" />
    <style>
        body {
            background-color: rgb(247, 247, 247);
        }

        body,
        html {
            height: 100%;
        }
    </style>
</head>

<body>
    <div class="backButton">
        <a href="../"><img class="backbuttonlink" src="../../Extra/styles/images/backButton.png" alt="Back Button" width="50px" height="50px" /></a>
    </div>
    <br /><br /><br />
    <div class="outer">
        <div class="editMainBox">
            <div class="editProfileBox">
                <div class="profileUserInfo">
                    <?php
                    require_once("../../DB.php");
                    $query = "SELECT picture FROM users WHERE BINARY username=?";
                    $stmt = mysqli_prepare($conn, $query);
                    $who = $_SESSION["who"];
                    mysqli_stmt_bind_param($stmt, "s", $who);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    if (mysqli_num_rows($result)) {
                        $row = mysqli_fetch_row($result);
                        echo
                        "
                        <img src='../View_Image/?u=" . $_SESSION["who"] . "' width='100px' height='100px' style='border-radius:50%'/>
                        <h2 style='color:yellow'>" . $_SESSION["who"] . "</h2>
                       ";
                    } else {
                        destroy();
                    }
                    function destroy()
                    {
                        session_destroy();
                        header("Location:../../");
                    }
                    ?>
                </div>
                <div class="profileLinks">
                    <a class="link profileLink" href="Edit_Profile/">Change Picture</a>
                    <a class="link profileLink" href="Change_Password/">Change Password</a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>


<?php
mysqli_close($conn);
?>