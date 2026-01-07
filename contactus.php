<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $subject = trim($_POST["subject"]); 
    $message = trim($_POST["message"]);

    if (empty($name) || empty($email) || empty($message) || empty($subject) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Oops! There was a problem with your submission. Please complete the form and try again.";
        exit;
    }

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'kwmcsventuress@gmail.com';
        $mail->Password   = 'akow fbrk idtx ksbp'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom($email, $name);
        $mail->addAddress('kwmcsventuress@gmail.com'); 
        $mail->isHTML(false);
        $mail->Body    = "Name: $name\n";
        $mail->Body   .= "Email: $email\n\n";
        $mail->Body   .= "Subject: $subject\n\n";
        $mail->Body   .= "Message:\n$message\n";

        $mail->send();

        echo "<script>alert('Thank You! Your message has been sent.');</script>";
        echo "<script>history.back();</script>";
        exit;
    } catch (Exception $e) {
        echo "Oops! Something went wrong and we couldn't send your message. Error: " . $mail->ErrorInfo;
        exit;
    }
} else {
    
    echo "There was a problem with your submission, please try again.";
    exit;
}

?>
