<?php 
include_once __DIR__.'/PHPMailer/src/PHPMailer.php';
include_once __DIR__.'/PHPMailer/src/SMTP.php';
include_once __DIR__.'/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;

function response($success, $data) {
    $res = ["success" => $success];
    $res["data"] = $data;
    return json_encode($res, JSON_UNESCAPED_SLASHES);
}

function sendEmail($subject, $body, $to, $embeddedImage = "", $attachment = "") {
    try {
        $mail = new PHPMailer(true);
    
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true; // tell phpmailer to authenticate with gmail
        $mail->Username = 'habitracker.noreply@gmail.com'; // email address
        $mail->Password = 'rguaamldaftqbucp';
        $mail->SMTPSecure = 'ssl'; // to use gmail need to connect ssl
        $mail->Port = '465';    
        $mail->AddAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
    
        if (!empty($embeddedImage)) {
            $mail->AddEmbeddedImage($embeddedImage, 'logo');
        }
        if (!empty($attachment)) {
            $mail->AddAttachment($attachment, $name = 'Weekly_Report.pdf', $encoding = 'base64', $type ='application/pdf');
        }
    
        $mail->Send();
    }
    catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>