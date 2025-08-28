<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/phpmailer/src/SMTP.php';
require_once __DIR__ . '/phpmailer/src/Exception.php';

function sendEmail($to_email, $subject, $body) {
    $mail = new PHPMailer(true);
    $mail->CharSet = 'UTF-8';
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; 
        $mail->SMTPAuth = true;
        $mail->Username = 'huahungtan001@gmail.com';       
        $mail->Password = 'ndgf jodj yhcx twor';           
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('huahungtan001@gmail.com', 'Yêu Sách');
        $mail->addAddress($to_email);

        $mail->isHTML(true);
        $mail->Subject = mb_encode_mimeheader($subject, "UTF-8");
        $mail->Body    = $body;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}
?>