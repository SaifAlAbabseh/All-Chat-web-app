<?php
require_once("env.php");
require_once("DB.php");
require_once("common.php");

session_start();

// Secure Remember Me via HttpOnly Cookie
if (!isset($_SESSION["who"]) && isset($_COOKIE['remember_user'])) {
    $cookie_data = explode(':', $_COOKIE['remember_user']);
    if (count($cookie_data) == 2) {
        $un = $cookie_data[0];
        $hash = $cookie_data[1];
        if (hash_equals(hash_hmac('sha256', $un, getVarFromEnv('DB_PASSWORD')), $hash)) {
            $_SESSION["who"] = $un;
            $av = "UPDATE users SET available='1' WHERE BINARY username=?";
            $stmt = mysqli_prepare($conn, $av);
            mysqli_stmt_bind_param($stmt, "s", $un);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            header("Location: Main/");
            exit();
        }
    }
}
if (isset($_SESSION) && isset($_SESSION["code"])) {
    $old_code = $_SESSION["code"];
    unset($_SESSION["code"]);
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>
        ALL CHAT
    </title>
    <meta name="viewport" content="width=device-width,initial-scale=1.0" />
    <link rel="stylesheet" href="<?= asset('Extra/styles/cssFiles/themes.css') ?>" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        localStorage.setItem('theme', 'light');
        document.documentElement.classList.add('light-mode');
        document.addEventListener('DOMContentLoaded', () => {
            document.body.classList.add('light-mode');
        });
        $(document).ready(function() {
            var temp = 1;
            temp = parseInt("<?php if (isset($_REQUEST) && isset($_REQUEST["t"])) {echo "0";} ?>");
            if (isNaN(temp)) {
                $("#main_box-id").css({
                    "pointer-events": "none",
                    "opacity": "0.2",
                    "user-select": "none"
                });
                $("#mainDialogBox").show();
            }
        });
    </script>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #f0f4f8 0%, #d9e2ec 100%) !important;
        }
        .main_box {
            background: rgba(255, 255, 255, 0.7) !important;
            backdrop-filter: blur(25px) !important;
            -webkit-backdrop-filter: blur(25px) !important;
            border: 1px solid rgba(255, 255, 255, 0.6) !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15) !important;
            overflow: hidden;
            display: flex;
            position: relative;
        }
        .allchat_image_box {
            background: rgba(255, 255, 255, 0.4) !important;
            z-index: 10;
        }
        .forms_wrapper {
            position: absolute;
            right: 0;
            width: 400px;
            height: 100%;
            overflow: hidden;
        }
        .forms_slider {
            display: flex;
            width: 800px;
            height: 100%;
            transition: transform 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }
        #login_box, #signup_box {
            width: 400px !important;
            height: 100%;
            position: relative !important;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transition: opacity 0.4s ease;
        }
        #signup_box {
            opacity: 0 !important;
            pointer-events: none !important;
        }
        .forms_wrapper.show-signup .forms_slider {
            transform: translateX(-400px);
        }
        .forms_wrapper.show-signup #login_box {
            opacity: 0 !important;
            pointer-events: none !important;
        }
        .forms_wrapper.show-signup #signup_box {
            opacity: 1 !important;
            pointer-events: all !important;
        }
        .inputfield {
            background: rgba(255, 255, 255, 0.9) !important;
            border: 1px solid rgba(0, 0, 0, 0.1) !important;
            border-radius: 8px !important;
            padding: 12px 15px !important;
            color: #333 !important;
            margin-bottom: 15px;
            width: 100%;
            box-sizing: border-box;
            transition: all 0.3s ease !important;
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.05);
        }
        .inputfield::placeholder {
            color: #888 !important;
        }
        .inputfield:focus {
            background: #fff !important;
            border-color: #0162AF !important;
            box-shadow: 0 0 15px rgba(1, 98, 175, 0.15), inset 0 1px 3px rgba(0,0,0,0.05) !important;
        }
        .buttontag {
            background: linear-gradient(135deg, #0162AF 0%, #004d8c 100%) !important;
            border: none !important;
            padding: 10px 25px !important;
            border-radius: 8px !important;
            color: white !important;
            font-weight: bold !important;
            box-shadow: 0 4px 15px rgba(1, 98, 175, 0.3) !important;
            transition: transform 0.2s, box-shadow 0.2s !important;
        }
        .buttontag:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(1, 98, 175, 0.5) !important;
        }
        .boxupperlabel {
            color: #102a43 !important;
            -webkit-text-stroke: 0 !important;
            font-size: 2.2rem;
            margin-bottom: 30px;
            text-shadow: none !important;
            font-weight: 800;
        }
        .secondaryButton, .goBackToLoginButton {
            color: #F76D57 !important;
            border: 1px solid #F76D57 !important;
            background: transparent !important;
            padding: 10px 20px !important;
            border-radius: 8px !important;
            cursor: pointer;
            transition: all 0.3s ease !important;
            font-weight: bold;
        }
        .goBackToLoginButton {
            position: absolute !important;
            top: 15px !important;
            left: 15px !important;
            font-size: 0.85rem !important;
            padding: 6px 12px !important;
        }
        #signup_box .boxupperlabel {
            margin-top: 35px !important;
        }
        .secondaryButton:hover, .goBackToLoginButton:hover {
            background: #F76D57 !important;
            color: white !important;
            box-shadow: 0 4px 15px rgba(247, 109, 87, 0.3) !important;
        }
        .small-labels, .orLabel {
            color: #486581 !important;
            font-weight: 600;
            -webkit-text-stroke: 0 !important;
        }
        .invalid {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            width: 80%;
            text-align: center;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(255,0,0,0.2);
            background: #fff !important;
            color: red !important;
            margin: 0 !important;
            z-index: 20;
        }
        .captchaInputField {
            width: 120px !important;
            margin-left: 10px !important;
            margin-bottom: 0 !important;
        }
        .captchaOuterBoxParent {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 5px;
            margin-bottom: 30px;
        }

        .mobile-logo {
            display: none;
            width: 150px;
            height: 60px;
            object-fit: contain;
            margin-top: 45px;
            margin-bottom: 5px;
            border-radius: 10px;
            mix-blend-mode: multiply;
        }

        /* Responsive Design for Mobile */
        @media (max-width: 850px) {
            .mobile-logo {
                display: block !important;
            }
            .main_box {
                width: 90vw !important;
                max-width: 450px !important;
                min-height: 600px !important;
                border-radius: 20px !important;
            }
            .allchat_image_box {
                display: none !important;
            }
            .forms_wrapper {
                width: 100% !important;
                position: relative !important;
                border-radius: 20px !important;
            }
            .forms_slider {
                width: 200% !important;
            }
            #login_box, #signup_box {
                width: 50% !important;
                border-radius: 20px !important;
            }
            .forms_wrapper.show-signup .forms_slider {
                transform: translateX(-50%) !important;
            }
            .boxupperlabel {
                font-size: 1.8rem !important;
            }
            .invalid {
                font-size: 0.9rem !important;
            }
        }
    </style>
</head>

<body>
    <div id="mainDialogBox">
        <button id="exit">X</button>
        <div id="main-dialog-inner">
            <a class="otherPlatformDownloadLink" download href="Extra/other_platforms/All Chat.exe">Desktop Version Download</a>
            <br>
            <a class="otherPlatformDownloadLink" download href="Extra/other_platforms/All Chat.apk">Android Version Download</a>
        </div>
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
    <div class="main_box" id="main_box-id">
        <div class="allchat_image_box">
            <img src="Extra/styles/images/main.png" alt="all_chat image" width="400px" height="400px" />
        </div>
        <div class="forms_wrapper" id="forms_wrapper">
            <div class="forms_slider" id="forms_slider">
            <div class="login_box" id="login_box">
                <img src="Extra/styles/images/main.png" alt="All Chat Logo" class="mobile-logo" />
                <h1 class="boxupperlabel">
                    Login
                </h1>
                <form action="?t=0" method="post" autocomplete="off" class="forms loginForm">
                    <table>
                        <tr>
                            <td colspan="2">
                                <input type="text" name="username" class="inputfield" id="username_inputfield" placeholder="Enter Username.." required />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <input type="password" name="userpassword" class="inputfield" id="userpassword_field" placeholder="Enter Password.." required />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" align="left">
                                <input type="checkbox" name="rememberCred" id="rememberCred" class="checkbox">
                                <label class="small-labels" for="rememberCred">Remember</label>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <br>
                                <input type="submit" name="loginButton" class="buttontag" id="login_buttontag" value="LOGIN" />
                            </td>
                            <td>
                                <br>
                                <input type="reset" name="login_clearbutton" class="buttontag" id="login_buttontagclear" value="CLEAR" />
                            </td>
                        </tr>
                    </table>
                    <h2 class="invalid" id="invalidforlogin">
                        INVALID
                    </h2>
                </form>
                <h1 class="orLabel">
                    OR
                </h1>
                <button class="secondaryButton" onclick="switchToSignupForm(true)">
                    Create an account
                </button>
            </div>
            <div class="signup_box" id="signup_box">
                <button class="goBackToLoginButton" onclick="switchToSignupForm(false)">
                    &lt; Go back to login
                </button>
                <img src="Extra/styles/images/main.png" alt="All Chat Logo" class="mobile-logo" />
                <h1 class="boxupperlabel">
                    Sign Up
                </h1>
                <form action="?t=0" method="post" autocomplete="off" class="forms signupForm">
                    <table>
                        <tr>
                            <td colspan="2">
                                <input type="email" name="signupemail" class="inputfield" id="signupemail_inputfield" placeholder="Enter Your Email.." required />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <input type="text" name="signupusername" class="inputfield" id="signupusername_inputfield" placeholder="Enter Username.." required />
                            </td>
                            <td>
                                <input type="button" value=" i " class="i" id="userreq">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <input type="password" name="signupuserpassword" class="inputfield" id="signupuserpassword_field" placeholder="Enter Password.." required />
                            </td>
                            <td>
                                <input type="button" value=" i " class="i" id="passreq">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <input type="password" name="signupuserconfirmpassword" class="inputfield" id="signupuserconfirmpassword_field" placeholder="Confirm Password.." required />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <br>
                                <input type="submit" name="signupButton" class="buttontag" id="signup_buttontag" value="SIGNUP" />
                            </td>
                            <td>
                                <br>
                                <input type="reset" name="signup_clearbutton" class="buttontag" id="signup_buttontagclear" value="CLEAR" />
                            </td>
                        </tr>
                    </table>
                    <br>
                    <div class="captchaOuterBoxParent">
                        <?php

                        function showCaptcha()
                        {
                            $_SESSION["code"] = "";

                            $how_many_chars = rand(4, 6);
                            for ($i = 1; $i <= $how_many_chars; $i++) {
                                $_SESSION["code"] .= generateRandomChar();
                            }

                            echo "<img width='200' height='50' src='captcha.php' id='captchaBox'>";
                        }

                        showCaptcha();

                        ?>
                        <input type="text" name="signup_code_field" class="inputfield captchaInputField" placeholder="Code.." required />
                    </div>
                    <h2 class="invalid" id="invalidforsignup">
                        INVALID
                    </h2>
                </form>
            </div>
        </div>
        </div>
    </div>




    <script src="<?= asset('scripts/index.js') ?>"></script>
</body>

</html>
<?php

function generateSignupCode()
{
    $_SESSION["signup_code"] = "";
    $how_many_chars = rand(4, 8);
    for ($i = 1; $i <= $how_many_chars; $i++) {
        $_SESSION["signup_code"] .= generateRandomChar();
    }
}

if (isset($_POST) && (isset($_POST["loginButton"]) || isset($_POST["signupButton"]) || isset($_POST["verification_button"]))) {
    echo "<script>startLoading('main_box-id');</script>";
    extract($_POST);

    if (isset($loginButton)) {
        $un = $username;
        $password = trim($userpassword);
        if (trim($un) != "" && $password != "") {

            $query = "SELECT password FROM users WHERE BINARY username=?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "s", $un);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $login_success = false;
            
            if (mysqli_num_rows($result)) {
                $row = mysqli_fetch_assoc($result);
                $db_pass = $row['password'];
                if (password_verify($password, $db_pass) || md5($password) === $db_pass) {
                    $login_success = true;
                    // Transparent migration from MD5 to BCrypt
                    if (md5($password) === $db_pass) {
                        $new_hash = password_hash($password, PASSWORD_BCRYPT);
                        $upd = "UPDATE users SET password=? WHERE BINARY username=?";
                        $upd_stmt = mysqli_prepare($conn, $upd);
                        mysqli_stmt_bind_param($upd_stmt, "ss", $new_hash, $un);
                        mysqli_stmt_execute($upd_stmt);
                        mysqli_stmt_close($upd_stmt);
                    }
                }
            }
            mysqli_free_result($result);
            mysqli_stmt_close($stmt);

            if ($login_success) {
                    $_SESSION["who"] = $un;
                    $av = "UPDATE users SET available='1' WHERE BINARY username=?";
                    $stmt = mysqli_prepare($conn, $av);
                    mysqli_stmt_bind_param($stmt, "s", $un);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                    
                    if (isset($rememberCred)) {
                        $hash = hash_hmac('sha256', $un, getVarFromEnv('DB_PASSWORD'));
                        $cookie_value = $un . ':' . $hash;
                        setcookie('remember_user', $cookie_value, time() + (86400 * 30), "/", "", false, true);
                    }
                    echo "<script>window.location.replace('Main/');</script>";
            } else {
                echo
                "<script>
                     var invalidfield=document.getElementById('invalidforlogin');
                     invalidfield.style.display='block';
                </script>";
            }
        } else {
            echo
            "<script>
                var invalidfield=document.getElementById('invalidforlogin');
                invalidfield.style.display='block';
            </script>";
        }
    } else if (isset($signupButton)) {
        $un = $signupusername;
        $password = trim($signupuserpassword);
        $cpass = trim($signupuserconfirmpassword);
        if (trim($un) != "" && $password != "" && $cpass != "") {
            $everythingIsGood = true;
            if (strpos($un, " ") || !(strlen($un) >= 6 && strlen($un) <= 12)) {
                $everythingIsGood = false;
            }
            if ($everythingIsGood) {
                for ($i = 0; $i < strlen($un); $i++) {
                    if (!((ord(substr($un, $i, 1)) >= 48 && ord(substr($un, $i, 1)) <= 57) || (ord(substr($un, $i, 1)) >= 65 && ord(substr($un, $i, 1)) <= 90) || (ord(substr($un, $i, 1)) >= 97 && ord(substr($un, $i, 1)) <= 122))) {
                        $everythingIsGood = false;
                        break;
                    }
                }
            }
            if (strpos($password, " ") || !(strlen($password) >= 8 && strlen($password) <= 16)) {
                $everythingIsGood = false;
            }
            if ($everythingIsGood) {
                if ($password === $cpass) {
                    $password = password_hash($password, PASSWORD_BCRYPT);
                    $query = "SELECT username FROM users WHERE BINARY username=?";
                    $stmt = mysqli_prepare($conn, $query);
                    mysqli_stmt_bind_param($stmt, "s", $un);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    $username_exists = mysqli_num_rows($result) > 0;
                    mysqli_free_result($result);
                    mysqli_stmt_close($stmt);
                    
                    $query_email_check = "SELECT email FROM users WHERE BINARY email=?";
                    $stmt2 = mysqli_prepare($conn, $query_email_check);
                    mysqli_stmt_bind_param($stmt2, "s", $signupemail);
                    mysqli_stmt_execute($stmt2);
                    $result_email_check = mysqli_stmt_get_result($stmt2);
                    $email_exists = mysqli_num_rows($result_email_check) > 0;
                    mysqli_free_result($result_email_check);
                    mysqli_stmt_close($stmt2);
                    if ($username_exists) {
                            echo
                            "<script>
                                switchToSignupForm(true);
                                var invalidfield=document.getElementById('invalidforsignup');
                                invalidfield.style.display='block';
                                invalidforsignup.innerHTML='Username is already in use';
                            </script>";
                        } else if ($email_exists) {
                            echo
                            "<script>
                                switchToSignupForm(true);
                                var invalidfield=document.getElementById('invalidforsignup');
                                invalidfield.style.display='block';
                                invalidforsignup.innerHTML='Email is already in use';
                            </script>";
                        } else {
                            if ($signup_code_field == $old_code) {
                                generateSignupCode();
                                $_SESSION["u_email"] = $signupemail;
                                $_SESSION["u_username"] = $un;
                                $_SESSION["u_password"] = $password;
                                
                                require_once "mail.php";
                                if (sendVerificationCodeMail($signupemail, $un, $_SESSION["signup_code"])) {
                                    echo
                                    "<div id='email_verification_box_parent'>
                                    <div id='email_verification_box'>
                                        <h2 style='color:white'>We've sent an email verification code to your email</h2>
                                        <form action='' method='post'>
                                            <input type='text' name='verification_code_field' class='inputfield' placeholder='Enter your verification code'>
                                            <input type='submit' class='buttontag' name='verification_button' value='Verify'>
                                        </form>
                                    </div>
                                </div>";
                                } else {
                                    echo
                                    "<script>
                                        switchToSignupForm(true);
                                        var invalidfield=document.getElementById('invalidforsignup');
                                        invalidfield.style.display='block';
                                        invalidfield.style.color='red';
                                        invalidforsignup.innerHTML='Email could not be sent error';
                                    </script>";
                                }
                            } else {
                                echo
                                "<script>
                                    switchToSignupForm(true);
                                    var invalidfield=document.getElementById('invalidforsignup');
                                    invalidfield.style.display='block';
                                    invalidforsignup.innerHTML='Invalid captcha code, try again';
                                </script>";
                            }

                    }
                } else {
                    echo
                    "<script>
                    switchToSignupForm(true);
                    var invalidfield=document.getElementById('invalidforsignup');
                    invalidfield.style.display='block';
                    invalidforsignup.innerHTML='Passwords does not match!';
                    </script>";
                }
            } else {
                echo
                        "<script>
                            switchToSignupForm(true);
                            var invalidfield=document.getElementById('invalidforsignup');
                            invalidfield.style.display='block';
                            invalidforsignup.innerHTML='Error';
                        </script>";
            }
        } else {
            echo
            "<script>
                switchToSignupForm(true);
                var invalidfield=document.getElementById('invalidforsignup');
                invalidfield.style.display='block';
            </script>";
        }
    } else if (isset($verification_button) && isset($_SESSION) && isset($_SESSION["u_email"]) && isset($_SESSION["u_username"]) && isset($_SESSION["u_password"]) && isset($verification_code_field)) {
        if ($verification_code_field == $_SESSION["signup_code"]) {
            $squery = "INSERT INTO users(username, password, picture, available, email) VALUES (?,?,?,?,?)";
            $stmt = mysqli_prepare($conn, $squery);
            $pic = 'user';
            $ava = '0';
            mysqli_stmt_bind_param($stmt, "sssss", $_SESSION["u_username"], $_SESSION["u_password"], $pic, $ava, $_SESSION["u_email"]);
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                echo
                "<script>
                switchToSignupForm(true);
                var invalidfield=document.getElementById('invalidforsignup');
                invalidfield.style.display='block';
                invalidfield.style.color='green';
                invalidforsignup.innerHTML='Successful';
            </script>";
            } else {
                mysqli_stmt_close($stmt);
                echo
                "<script>
                switchToSignupForm(true);
                var invalidfield=document.getElementById('invalidforsignup');
                invalidfield.style.display='block';
                invalidforsignup.innerHTML='Connection Error';
            </script>";
            }
        } else {
            echo
                        "<script>
                            switchToSignupForm(true);
                            var invalidfield=document.getElementById('invalidforsignup');
                            invalidfield.style.display='block';
                            invalidforsignup.innerHTML='Invalid Code';
                        </script>";
        }
        unset($_SESSION["signup_code"]);
        unset($_SESSION["u_email"]);
        unset($_SESSION["u_username"]);
        unset($_SESSION["u_password"]);
    }
    mysqli_close($conn);
}


?>