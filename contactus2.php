<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

// NOTE: We removed the JSON header so we can output the Script/HTML below directly.

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $message = trim($_POST["message"]);

    // Validation: Check for empty fields
    if (empty($name) || empty($email) || empty($message) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>
                alert('Oops! Please complete all fields correctly.');
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
        $mail->Subject = "New Inquiry from Footer Form";
        $mail->Body    = "Name: $name\n";
        $mail->Body   .= "Email: $email\n\n";
        $mail->Body   .= "Message:\n$message\n";

        $mail->send();

        // --- SUCCESS POP-UP ---
        // This script runs, shows the alert, and then reloads your contact page.
        echo "<script>
                alert('Thank You! Your message has been sent.');
                window.location.href = 'contact.html'; 
              </script>";
        exit;

    } catch (Exception $e) {
        // --- ERROR POP-UP ---
        echo "<script>
                alert('Message could not be sent. Error: {$mail->ErrorInfo}');
                history.back();
              </script>";
        exit;
    }
} else {
    // --- INVALID ACCESS POP-UP ---
    echo "<script>
            alert('There was a problem with your submission. Please try again.');
            history.back();
          </script>";
    exit;
}
?>