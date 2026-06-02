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
    </style>
</head>

<body>
    <div style="position: absolute; top: 20px; left: 20px; z-index: 1000;">
        <a href="../" class="backButton" style="text-decoration:none; font-size:1.5rem; color: #F76D57; font-weight:bold; background:rgba(255,255,255,0.1); padding:10px 20px; border-radius:20px; backdrop-filter:blur(5px); transition:all 0.3s ease;">🔙 Back</a>
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
                        <h2 class='profileUsername'>" . $_SESSION["who"] . "</h2>
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
                    <a class="link btn-primary profileLink" href="Edit_Profile/">Change Picture</a>
                    <a class="link btn-primary profileLink" href="Change_Password/">Change Password</a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>


<?php
mysqli_close($conn);
?>