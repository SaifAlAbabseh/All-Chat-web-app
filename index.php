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
    <script src="scripts/index.js"></script>
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

        function setSavedCreds() {
            if (localStorage.getItem("all_chat-username") != null && localStorage.getItem("all_chat-password") != null) {
                document.getElementById("username_inputfield").value = localStorage.getItem("all_chat-username");
                document.getElementById("userpassword_field").value = localStorage.getItem("all_chat-password");
            }
        }

        function hideMainBoxBeforeLoading(box_id) {
            $("#" + box_id + "").css({
                'pointer-events': 'none',
                'user-select': 'none'
            });
            $("#" + box_id + "").animate({
                opacity: 0
            }, 500);
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

        function rememberCreds(username, password) {
            localStorage.setItem("all_chat-username", username);
            localStorage.setItem("all_chat-password", password);
        }
    </script>
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
        <div class="login_box">
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
                        <td colspan="2">
                            <input type="checkbox" name="rememberCred" id="rememberCred" class="checkbox">
                            <label class="small-labels" for="rememberCred">Remember Credentials</label>
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
                    <tr>
                        <th colspan="2">
                            <h2 class="invalid" id="invalidforlogin">
                                INVALID
                            </h2>
                        </th>
                    </tr>
                </table>
            </form>
        </div>
        <div class="signup_box">
            <h1 class="boxupperlabel">
                Sign Up
            </h1>
            <form action="?t=0" method="post" autocomplete="off" class="forms">
                <table>
                    <tr>
                        <td colspan="2">
                            <input type="email" name="signupemail" class="inputfield" id="signupemail_inputfield" placeholder="Enter Your Email.." required />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <input type="text" name="signupusername" class="inputfield" id="signupusername_inputfield" placeholder="Enter A Username.." required />
                        </td>
                        <td>
                            <input type="button" value=" i " class="i" id="userreq">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <input type="password" name="signupuserpassword" class="inputfield" id="signupuserpassword_field" placeholder="Enter A Password.." required />
                        </td>
                        <td>
                            <input type="button" value=" i " class="i" id="passreq">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <input type="password" name="signupuserconfirmpassword" class="inputfield" id="signupuserconfirmpassword_field" placeholder="Re-Enter The Password.." required />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
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

                                echo "<img width='200' height='50' src='captcha.php'>";
                            }

                            showCaptcha();

                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <input type="text" name="signup_code_field" class="inputfield" placeholder="Enter Code" required />
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
                    <tr>
                        <th colspan="3">
                            <h2 class="invalid" id="invalidforsignup">
                                INVALID
                            </h2>
                        </th>
                    </tr>
                </table>
            </form>
        </div>
    </div>
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
    require_once("DB.php");
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
                    $query_email_check = "SELECT email FROM users WHERE BINARY email='".$signupemail."'";
                    $result = mysqli_query($conn, $query);
                    $result_email_check = mysqli_query($conn, $query_email_check);
                    if ($result && $result_email_check) {
                        if (mysqli_num_rows($result)) {
                            echo
                            "<script>
                                var invalidfield=document.getElementById('invalidforsignup');
                                invalidfield.style.display='block';
                                invalidforsignup.innerHTML='Username is already in use';
                            </script>";
                        }
                        else if(mysqli_num_rows($result_email_check)) {
                            echo
                            "<script>
                                var invalidfield=document.getElementById('invalidforsignup');
                                invalidfield.style.display='block';
                                invalidforsignup.innerHTML='Email is already in use';
                            </script>";
                        }
                        else {
                            if ($signup_code_field == $old_code) {
                                generateSignupCode();
                                $_SESSION["u_email"] = $signupemail;
                                $_SESSION["u_username"] = $un;
                                $_SESSION["u_password"] = $password;
                                if(sendMail("allchatbot1@gmail.com", "All Chat", $signupemail, "User", "All Chat Email Verification Code", "Your code is: " . $_SESSION["signup_code"])) {
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
                                        var invalidfield=document.getElementById('invalidforsignup');
                                        invalidfield.style.display='block';
                                        invalidfield.style.color='green';
                                        invalidforsignup.innerHTML='Error';
                                    </script>";
                                }
                            } else {
                                echo
                                "<script>
                                    var invalidfield=document.getElementById('invalidforsignup');
                                    invalidfield.style.display='block';
                                    invalidforsignup.innerHTML='Invalid captcha code, try again';
                                </script>";
                            }
                        }
                    } else {
                        echo
                        "<script>
                            var invalidfield=document.getElementById('invalidforsignup');
                            invalidfield.style.display='block';
                            invalidforsignup.innerHTML='Connection Error';
                        </script>";
                    }
                } else {
                    echo
                    "<script>
                    var invalidfield=document.getElementById('invalidforsignup');
                    invalidfield.style.display='block';
                    invalidforsignup.innerHTML='Passwords does not match!';
                    </script>";
                }
            } else {
                echo
                "<script>
                            var invalidfield=document.getElementById('invalidforsignup');
                            invalidfield.style.display='block';
                            invalidforsignup.innerHTML='Error';
                        </script>";
            }
        } else {
            echo
            "<script>
                var invalidfield=document.getElementById('invalidforsignup');
                invalidfield.style.display='block';
            </script>";
        }
    }
    else if(isset($verification_button) && isset($_SESSION) && isset($_SESSION["u_email"]) && isset($_SESSION["u_username"]) && isset($_SESSION["u_password"]) && isset($verification_code_field)) {
        if($verification_code_field == $_SESSION["signup_code"]) {
            $squery = "INSERT INTO users VALUES ('" . $_SESSION["u_username"] . "','" . $_SESSION["u_password"] . "','user','0', '".$_SESSION["u_email"]."')";
            if (mysqli_query($conn, $squery)) {
                echo
                "<script>
                var invalidfield=document.getElementById('invalidforsignup');
                invalidfield.style.display='block';
                invalidfield.style.color='green';
                invalidforsignup.innerHTML='Successful';
            </script>";
            }
            else {
                echo
                "<script>
                var invalidfield=document.getElementById('invalidforsignup');
                invalidfield.style.display='block';
                invalidforsignup.innerHTML='Connection Error';
            </script>";
            }
        }
        else {
            echo
                "<script>
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

function sendMail($from_email, $from_name, $to_email, $to_name, $subject, $body) {
    
    require $_SERVER['DOCUMENT_ROOT'] . '/All_Chat/mail/Exception.php';
    require $_SERVER['DOCUMENT_ROOT'] . '/All_Chat/mail/PHPMailer.php';
    require $_SERVER['DOCUMENT_ROOT'] . '/All_Chat/mail/SMTP.php';
    
    $mail = new PHPMailer;
    $mail->isSMTP(); 
    $mail->SMTPDebug = 2; // 0 = off (for production use) - 1 = client messages - 2 = client and server messages
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