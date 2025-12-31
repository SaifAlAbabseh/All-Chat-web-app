<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendMail($to_email, $to_name, $code)
{

    require $_SERVER['DOCUMENT_ROOT'] . '/' . $GLOBALS['urlMainPath'] . '/mail/Exception.php';
    require $_SERVER['DOCUMENT_ROOT'] . '/' . $GLOBALS['urlMainPath'] . '/mail/PHPMailer.php';
    require $_SERVER['DOCUMENT_ROOT'] . '/' . $GLOBALS['urlMainPath'] . '/mail/SMTP.php';

    $from_email = "allchatbot1@gmail.com";
    $from_name = "All Chat";
    $subject = "All Chat Email Verification Code";
    $site_name = "All Chat";

    $template = file_get_contents("email_templates/signup_verification_template.html");
    $body = str_replace(
        ['{{CODE}}', '{{YEAR}}', '{{SITE_NAME}}'],
        [$code, date('Y'), $site_name],
        $template
    );

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
