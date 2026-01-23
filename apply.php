<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Ensure these paths match your actual folder structure
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid access method.']);
    exit;
}

// Collect fields
$full_name = trim($_POST['full_name'] ?? '');
$email     = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$phone     = trim($_POST['phone'] ?? ''); // Now receives full number from JS fix
$status    = trim($_POST['status'] ?? '');
$education = trim($_POST['education'] ?? '');
$portfolio = trim($_POST['portfolio'] ?? '');
$linkedin  = trim($_POST['linkedin'] ?? '');
$role      = trim($_POST['role'] ?? 'Application');

// Validation
$missing = [];
if ($full_name === '') $missing[] = 'Full name';
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $missing[] = 'Valid email';
if ($phone === '') $missing[] = 'Phone';
if ($education === '') $missing[] = 'Education';
if ($linkedin === '') $missing[] = 'LinkedIn';
if (!isset($_FILES['resume']) || $_FILES['resume']['error'] !== UPLOAD_ERR_OK) $missing[] = 'Resume upload';

if (!empty($missing)) {
    header('Content-Type: application/json');
    http_response_code(422);
    $msg = 'Please provide: ' . implode(', ', $missing);
    echo json_encode(['success' => false, 'message' => $msg]);
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

    $mail->setFrom($email, 'KWMCS Ventures Careers');
    $mail->addReplyTo($email, $full_name);
    $mail->addAddress('kwmcsventuress@gmail.com');

    $mail->isHTML(false);
    $mail->Subject = "Application: {$role} - {$full_name}";

    $body  = "Candidate Name: {$full_name}\n";
    $body .= "Candidate Email: {$email}\n";
    $body .= "Phone: {$phone}\n";
    $body .= "Role: {$role}\n";
    $body .= "Status: {$status}\n";
    $body .= "Education: {$education}\n";
    $body .= "Portfolio: {$portfolio}\n";
    $body .= "LinkedIn: {$linkedin}\n";

    $mail->Body = $body;

    // Secure attachment handling
    if (is_uploaded_file($_FILES['resume']['tmp_name'])) {
        $mail->addAttachment($_FILES['resume']['tmp_name'], $_FILES['resume']['name']);
    } else {
        throw new Exception('File upload failed security check.');
    }

    $mail->send();

    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'Application sent successfully. Thank you!']);
    exit;
} catch (Exception $e) {
    $err = $mail->ErrorInfo ?: $e->getMessage();
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Message could not be sent. Error: ' . $err]);
    exit;
}
?>