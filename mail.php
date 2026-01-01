<?php

function sendFriendRequestMail($conn, $urlMainPath, $to_email, $to_name, $requester_username)
{
    require_once "server_info.php";
    require_once "common.php";

    $subject = "Someone Wants To Be Your Friend";
    $template = file_get_contents(__DIR__ . "/email_templates/friend_request_template.html");
    $site_name = "All Chat";

    $imageName = getUserImageName($conn, $requester_username);
    $hostPath = generateHostPath($urlMainPath, $baseUrl);
    $imagePath = $hostPath . "/Extra/styles/images/users_images/" . $imageName . ".png";

    $body = str_replace(
        ['{{REQUESTER_USERNAME}}', '{{YEAR}}', '{{SITE_NAME}}'],
        [$requester_username, date('Y'), $site_name],
        $template
    );
    return sendMail([true, $imagePath], $to_email, $to_name, $subject, $body);
}

function sendVerificationCodeMail($to_email, $to_name, $code)
{
    $subject = "All Chat Email Verification Code";
    $template = file_get_contents(__DIR__ . "/email_templates/signup_verification_template.html");
    $site_name = "All Chat";
    $body = str_replace(
        ['{{CODE}}', '{{YEAR}}', '{{SITE_NAME}}'],
        [$code, date('Y'), $site_name],
        $template
    );
    return sendMail([false, null], $to_email, $to_name, $subject, $body);
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendMail($isImage, $to_email, $to_name, $subject, $body)
{

    require 'mail/Exception.php';
    require 'mail/PHPMailer.php';
    require 'mail/SMTP.php';

    $from_email = "allchatbot1@gmail.com";
    $from_name = "All Chat";

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
    if ($isImage[0]) {
        $mail->addEmbeddedImage($isImage[1], 'logo_cid');
    }
    $mail->isHTML(true);
    $mail->Body = $body;
    // $mail->msgHTML($body); //$mail->msgHTML(file_get_contents('contents.html'), __DIR__); //Read an HTML message body from an external file, convert referenced images to embedded,
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