<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

// We removed the JSON header so we can output HTML/JS script tags instead
// header('Content-Type: application/json'); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $subject = trim($_POST["subject"]); 
    $message = trim($_POST["message"]);

    if (empty($name) || empty($email) || empty($message) || empty($subject) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Javascript Popup for Error
        echo "<script>
                alert('Oops! Please complete all fields correctly and try again.');
                history.back();
              </script>";
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

        $mail->Subject = "Website Inquiry: " . $subject; 

        $mail->Body    = "Name: $name\n";
        $mail->Body   .= "Email: $email\n\n";
        $mail->Body   .= "Subject: $subject\n\n";
        $mail->Body   .= "Message:\n$message\n";

        $mail->send();

        // --- SUCCESS POPUP ---
        // This script runs, shows the alert, and then redirects the user to 'contact.html'
        echo "<script>
                alert('Thank You! Your message has been sent successfully.');
                window.location.href = 'contact.html'; 
              </script>";
        exit;
        
    } catch (Exception $e) {
        // Javascript Popup for Mailer Error
        echo "<script>
                alert('Message could not be sent. Error: " . addslashes($mail->ErrorInfo) . "');
                history.back();
              </script>";
        exit;
    }
} else {
    echo "<script>
            alert('There was a problem with your submission, please try again.');
            history.back();
          </script>";
    exit;
}
?>