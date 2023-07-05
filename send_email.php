<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require ('../vendor/autoload.php');
include ('../mail/mailer.php');

function send_email($to, $subject, $body)
{
    $config = array (
        'email' => array (
            'host' => 'smtp.gmail.com',
            'username' => 'aitlimamkhadija96@gmail.com',
            'password' => 'xsqourooqdxuswby',
            'port' => 587,
            'admin_email' => 'aitlimamkhadija96@gmail.com',
            'admin_name' => 'Smarters Pro',
        ),
    );

    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->SMTPDebug = SMTP::DEBUG_OFF; 
        $mail->isSMTP();
        $mail->Host       = $config['email']['host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $config['email']['username'];
        $mail->Password   = $config['email']['password'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = $config['email']['port'];

        // Recipients
        $mail->setFrom($config['email']['admin_email'], $config['email']['admin_name']);
        $mail->addAddress($to);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
?>