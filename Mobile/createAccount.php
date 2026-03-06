<?php

require_once "../env.php";
require_once "../DB.php";
require_once "../common.php";

session_start();
if (isset($_SESSION) && isset($_SESSION["code"])) {
    $old_code = $_SESSION["code"];
    unset($_SESSION["code"]);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= asset('../Extra/styles/cssFiles/themes.css') ?>" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <title>Mobile Sign Up</title>
</head>

<body>
    <div class="signup_box">
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
                </tr>
                <tr>
                    <td colspan="2">
                        <input type="password" name="signupuserpassword" class="inputfield" id="signupuserpassword_field" placeholder="Enter Password.." required />
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

                    echo "<img width='200' height='50' src='../captcha.php' id='captchaBox'>";
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


if (isset($_POST["signupButton"]) || isset($_POST["verification_button"])) {
    extract($_POST);
    if (isset($signupButton)) {
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
                                
                                var invalidfield=document.getElementById('invalidforsignup');
                                invalidfield.style.display='block';
                                invalidforsignup.innerHTML='Username is already in use';
                            </script>";
                    } else if ($email_exists) {
                        echo
                        "<script>
                                
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

                            require_once "../mail.php";
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
                                        
                                        var invalidfield=document.getElementById('invalidforsignup');
                                        invalidfield.style.display='block';
                                        invalidfield.style.color='red';
                                        invalidforsignup.innerHTML='Email could not be sent error';
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
                
                var invalidfield=document.getElementById('invalidforsignup');
                invalidfield.style.display='block';
                invalidfield.style.color='green';
                invalidforsignup.innerHTML='Successful';
            </script>";
            } else {
                mysqli_stmt_close($stmt);
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
