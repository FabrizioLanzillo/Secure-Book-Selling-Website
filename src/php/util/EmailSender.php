<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailSender {
    private static $instance = null;
    private $mail;

    private function __construct() {
        require '/home/bookselling/composer/vendor/autoload.php';

        $this->mail = new PHPMailer(true);
        $this->mail->isSMTP();
        $this->mail->Host = 'smtp.gmail.com';
        $this->mail->SMTPAuth = true;
        $this->mail->Username = 'bookselling58@gmail.com';
        $this->mail->Password =  getenv("APACHE_EMAIL_SENDER_PASSWORD");
        $this->mail->SMTPSecure = 'ssl';
        $this->mail->Port = 465;
        $this->mail->setFrom('bookselling58@gmail.com', 'bookselling');
    }

    public static function getInstance(): ?EmailSender{
        if (self::$instance == null) {
            self::$instance = new EmailSender();
        }

        return self::$instance;
    }

    public function sendEmail($email, $subject, $title, ...$paragraphs): bool{
        $this->mail->addAddress($email);
        $this->mail->isHTML(true);
        $this->mail->Subject = $subject;

        // Crea il layout dell'email
        $body = '<h1>' . $title . '</h1>';
        foreach ($paragraphs as $paragraph) {
            $body .= '<p>' . $paragraph . '</p>';
        }

        $this->mail->Body = $body;

        if(!$this->mail->send())
            return false;

        return true;
    }
}
