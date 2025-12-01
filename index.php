<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();
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
    <link rel="stylesheet" href="Extra/styles/cssFiles/themes.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            setSavedCreds();
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
        }
    </style>
</head>

<body>
    <div id="mainDialogBox">
        <button id="exit">X</button>
        <div id="main-dialog-inner">
            <a class="otherPlatformDownloadLink" download href="Extra/Other Platforms/All Chat.exe">Desktop Version Download</a>
            <br>
            <a class="otherPlatformDownloadLink" download href="Extra/Other Platforms/All Chat.apk">Android Version Download</a>
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
        <div>
            <div class="login_box" id="login_box">
                <h1 class="boxupperlabel">
                    Login
                </h1>
                <form action="?t=0" method="post" autocomplete="off" class="forms">
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
                                <input type="submit" name="loginButton" class="buttontag" id="login_buttontag" value="LOGIN" />
                            </td>
                            <td>
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
                                <input type="submit" name="signupButton" class="buttontag" id="signup_buttontag" value="SIGNUP" />
                            </td>
                            <td>
                                <input type="reset" name="signup_clearbutton" class="buttontag" id="signup_buttontagclear" value="CLEAR" />
                            </td>
                        </tr>
                    </table>
                    <div class="captchaOuterBoxParent">
                        <?php

                        function generateRandomChar()
                        {
                            $capital_alphabets = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
                            $small_alphabets = "abcdefghijklmnopqrstuvwxyz";
                            $numbers = "0123456789";

                            $char_rand_alpha_or_number = rand(1, 2);
                            if ($char_rand_alpha_or_number == 1) {
                                $small_or_capital = rand(1, 2);
                                if ($small_or_capital == 1) {
                                    return "" . $small_alphabets[rand(0, strlen($small_alphabets) - 1)];
                                } else if ($small_or_capital == 2) {
                                    return "" . $capital_alphabets[rand(0, strlen($capital_alphabets) - 1)];
                                }
                            } else if ($char_rand_alpha_or_number == 2) {
                                return "" . $numbers[rand(0, strlen($numbers) - 1)];
                            }
                        }

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




    <script src="scripts/index.js"></script>
</body>

</html>
<?php

require_once("DB.php");
require_once("common.php");

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

            $query = "SELECT * FROM users WHERE BINARY username='" . $un . "' AND BINARY password='" . md5($password) . "'";
            $result = mysqli_query($conn, $query);
            if ($result) {
                if (mysqli_num_rows($result)) {
                    $_SESSION["who"] = $un;
                    $av = "UPDATE users SET available='1' WHERE BINARY username='" . $un . "'";
                    mysqli_query($conn, $av);
                    if (isset($rememberCred)) {
                        echo "
                            <script>
                                if(localStorage.getItem('all_chat-username') != null && localStorage.getItem('all_chat-password') != null){
                                    localStorage.removeItem('all_chat-username');
                                    localStorage.removeItem('all_chat-password');
                                    localStorage.setItem('all_chat-username', '" . $un . "');
                                    localStorage.setItem('all_chat-password', '" . $password . "');
                                }
                                else{
                                    localStorage.setItem('all_chat-username', '" . $un . "');
                                    localStorage.setItem('all_chat-password', '" . $password . "');
                                }
                                
                                window.location.replace('Main/');
                            </script>";
                    } else {
                        echo "
                            <script>
                                window.location.replace('Main/');
                            </script>";
                    }
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
                         invalidforsignup.innerHTML='Connection Error';
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
                $password = md5($password);
                $cpass = md5($cpass);
                if ($password == $cpass) {
                    $query = "SELECT username FROM users WHERE BINARY username='" . $un . "'";
                    $query_email_check = "SELECT email FROM users WHERE BINARY email='" . $signupemail . "'";
                    $result = mysqli_query($conn, $query);
                    $result_email_check = mysqli_query($conn, $query_email_check);
                    if ($result && $result_email_check) {
                        if (mysqli_num_rows($result)) {
                            echo
                            "<script>
                                switchToSignupForm(true);
                                var invalidfield=document.getElementById('invalidforsignup');
                                invalidfield.style.display='block';
                                invalidforsignup.innerHTML='Username is already in use';
                            </script>";
                        } else if (mysqli_num_rows($result_email_check)) {
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
                                if (sendMail("allchatbot1@gmail.com", "All Chat", $signupemail, "User", "All Chat Email Verification Code", "Your code is: " . $_SESSION["signup_code"])) {
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
                                        invalidforsignup.innerHTML='Error';
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
                            invalidforsignup.innerHTML='Connection Error';
                        </script>";
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
            $squery = "INSERT INTO users VALUES ('" . $_SESSION["u_username"] . "','" . $_SESSION["u_password"] . "','user','0', '" . $_SESSION["u_email"] . "')";
            if (mysqli_query($conn, $squery)) {
                echo
                "<script>
                var invalidfield=document.getElementById('invalidforsignup');
                invalidfield.style.display='block';
                invalidfield.style.color='green';
                invalidforsignup.innerHTML='Successful';
            </script>";
            } else {
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

function sendMail($from_email, $from_name, $to_email, $to_name, $subject, $body)
{

    require $_SERVER['DOCUMENT_ROOT'] . '/' . $GLOBALS['urlMainPath'] . '/mail/Exception.php';
    require $_SERVER['DOCUMENT_ROOT'] . '/' . $GLOBALS['urlMainPath'] . '/mail/PHPMailer.php';
    require $_SERVER['DOCUMENT_ROOT'] . '/' . $GLOBALS['urlMainPath'] . '/mail/SMTP.php';

    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->SMTPDebug = 0; // 0 = off (for production use) - 1 = client messages - 2 = client and server messages
    $mail->Host = "smtp.gmail.com"; // use $mail->Host = gethostbyname('smtp.gmail.com'); // if your network does not support SMTP over IPv6
    $mail->Port = 465; // TLS only
    $mail->SMTPSecure = 'ssl'; // ssl is deprecated
    $mail->SMTPAuth = true;
    $mail->Username = 'allchatbot1@gmail.com'; // email
    $mail->Password = ''; // password
    $mail->setFrom($from_email, $from_name); // From email and name
    $mail->addAddress($to_email, $to_name); // to email and name
    $mail->Subject = $subject;
    $mail->msgHTML($body); //$mail->msgHTML(file_get_contents('contents.html'), __DIR__); //Read an HTML message body from an external file, convert referenced images to embedded,
    $mail->AltBody = 'HTML messaging not supported'; // If html emails is not supported by the receiver, show this body
    // $mail->addAttachment('images/phpmailer_mini.png'); //Attach an image file
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
    return $mail->send();
}
?>