<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid access method.']);
    exit;
}

$full_name = trim($_POST['full_name'] ?? '');
$email     = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$phone     = trim($_POST['phone'] ?? '');
$status    = trim($_POST['status'] ?? '');
$education = trim($_POST['education'] ?? '');
$portfolio = trim($_POST['portfolio'] ?? '');
$linkedin  = trim($_POST['linkedin'] ?? '');
$role      = trim($_POST['role'] ?? 'Application');

$missing = [];
if ($full_name === '') $missing[] = 'Full name';
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $missing[] = 'Valid email';
if ($phone === '') $missing[] = 'Phone';
if ($education === '') $missing[] = 'Education';
if ($linkedin === '') $missing[] = 'LinkedIn';
if (!isset($_FILES['resume']) || $_FILES['resume']['error'] !== UPLOAD_ERR_OK) $missing[] = 'Resume upload';

if (!empty($missing)) {
    http_response_code(422);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Please provide: ' . implode(', ', $missing)]);
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

    $mail->setFrom('careers@kwmcsventures.com', 'KWMCS Ventures Careers');
    $mail->addReplyTo($email, $full_name);
    $mail->addAddress('kwmcsventuress@gmail.com');

    $mail->isHTML(true);
    $mail->Subject = "New Application | {$role} | {$full_name}";

    $resumeCid = 'resumeFile';

    $mail->Body = '
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
</head>
<body style="margin:0;padding:0;background:#f4f6f8;font-family:Arial,Helvetica,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="padding:20px;">
<tr>
<td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:8px;overflow:hidden;">

<tr>
<td style="background:#0d6efd;padding:18px;text-align:center;">
<img src="https://www.kwmcsventures.com/assets/images/kwmcslogo.png"
     alt="KWMCS Ventures"
     style="max-width:300px;height:auto;display:block;margin:0 auto 10px;">
<p style="color:#ffffff;font-size:15px;margin:0;">Careers Portal</p>
</td>
</tr>

<tr>
<td style="padding:20px;">
<p style="font-size:14px;margin-bottom:15px;">
A new candidate has applied for the <strong>'.$role.'</strong> position.
</p>

<table width="100%" cellpadding="8" cellspacing="0" style="border-collapse:collapse;font-size:14px;">
<tr>
<td style="font-weight:bold;border-bottom:1px solid #eee;width:35%;">Candidate Name</td>
<td style="border-bottom:1px solid #eee;">'.$full_name.'</td>
</tr>
<tr>
<td style="font-weight:bold;border-bottom:1px solid #eee;">Email</td>
<td style="border-bottom:1px solid #eee;">'.$email.'</td>
</tr>
<tr>
<td style="font-weight:bold;border-bottom:1px solid #eee;">Phone</td>
<td style="border-bottom:1px solid #eee;">'.$phone.'</td>
</tr>
<tr>
<td style="font-weight:bold;border-bottom:1px solid #eee;">Education</td>
<td style="border-bottom:1px solid #eee;">'.$education.'</td>
</tr>
<tr>
<td style="font-weight:bold;border-bottom:1px solid #eee;">Status</td>
<td style="border-bottom:1px solid #eee;">'.$status.'</td>
</tr>
<tr>
<td style="font-weight:bold;border-bottom:1px solid #eee;">Portfolio</td>
<td style="border-bottom:1px solid #eee;">
<a href="'.$portfolio.'" style="color:#0d6efd;text-decoration:none;" target="_blank">'.$portfolio.'</a>
</td>
</tr>
<tr>
<td style="font-weight:bold;">LinkedIn</td>
<td>
<a href="'.$linkedin.'" style="color:#0d6efd;text-decoration:none;" target="_blank">'.$linkedin.'</a>
</td>
</tr>
</table>

<p style="margin-top:25px;font-size:15px;color:#777;text-align:center;">
Resume is attached with this email
</p>
</td>
</tr>

</table>
</td>
</tr>
</table>
</body>
</html>';

    if (is_uploaded_file($_FILES['resume']['tmp_name'])) {
        $mail->addAttachment($_FILES['resume']['tmp_name'], $_FILES['resume']['name']);
        $mail->addEmbeddedImage($_FILES['resume']['tmp_name'], $resumeCid, $_FILES['resume']['name']);
    } else {
        throw new Exception('File upload failed security check.');
    }

    $mail->send();

    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'Application sent successfully. Thank you!']);
    exit;

} catch (Exception $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Message could not be sent.']);
    exit;
}
?>
