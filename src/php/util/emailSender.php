<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '/home/bookselling/composer/vendor/autoload.php';

function sendOtpEmail($email, $otp) {
    $mail = new PHPMailer(true);

    $mail->isSMTP();                                  
    $mail->Host = 'smtp.gmail.com';                  
    $mail->SMTPAuth = true;                             
    $mail->Username = 'bookselling58@gmail.com';                
    $mail->Password = 'gfda vgts qcru tzmv';                 
    $mail->SMTPSecure = 'ssl';                          
    $mail->Port = 465;                                 

    $mail->setFrom('bookselling58@gmail.com', 'bookselling'); 
    $mail->addAddress($email); 

    $mail->isHTML(true); 
    $mail->Subject = 'BookSelling - Your Otp code'; 
    $mail->Body = '<h1>Hello!</h1><p>This is the otp ' . $otp . ' requested.</p><p>It will last for 5 minutes.</p>'; 

    if(!$mail->send())
        return false; 

    return true;
}
