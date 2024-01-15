<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * This class sends email to the users=
 */
class EmailSender
{
    private static ?EmailSender $instance = null;
    private $mail;

    /**
     * Constructor that imports and sets the mail configuration
     */
    private function __construct()
    {
        require '/home/bookselling/composer/vendor/autoload.php';

        $this->mail = new PHPMailer(true);
        $this->mail->isSMTP();
        $this->mail->Host = 'smtp.gmail.com';
        $this->mail->SMTPAuth = true;
        $this->mail->Username = 'bookselling58@gmail.com';
        $this->mail->Password = getenv("APACHE_EMAIL_SENDER_PASSWORD");
        $this->mail->SMTPSecure = 'ssl';
        $this->mail->Port = 465;
        $this->mail->setFrom('bookselling58@gmail.com', 'bookselling');
    }

    /**
     * This method returns the singleton instance of EmailSender.
     * If the instance doesn't exist, it creates one; otherwise, it returns the existing instance.
     * @return EmailSender
     */
    public static function getInstance(): ?EmailSender
    {
        if (self::$instance == null) {
            self::$instance = new EmailSender();
        }

        return self::$instance;
    }

    /**
     * This method creates and sends an email to a user
     * @param $email , is the email of the user to contact
     * @param $subject , is the subject of the mail
     * @param $title , is the title of the mail
     * @param ...$paragraphs , is a variadic param that contains all the paragraphs of the mail
     * @return bool
     */
    public function sendEmail($email, $subject, $title, ...$paragraphs): bool
    {
        $this->mail->addAddress($email);
        $this->mail->isHTML(true);
        $this->mail->Subject = $subject;

        // Creates the layout of the email
        $body = '<h1>' . $title . '</h1>';
        foreach ($paragraphs as $paragraph) {
            $body .= '<p>' . $paragraph . '</p>';
        }

        $this->mail->Body = $body;

        if (!$this->mail->send())
            return false;

        return true;
    }
}
