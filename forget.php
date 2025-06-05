<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php';

header("Content-Type: application/json"); // Set response type

// Check if the request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["error" => "Invalid request method. Use POST."]);
    exit;
}

// Get email from POST request
$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['email'])) {
    echo json_encode(["error" => "Email is required"]);
    exit;
}

$email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);

// Generate a 6-digit OTP
$otp = rand(100000, 999999);

// Email Configuration
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.example.com';  // Replace with your SMTP server
    $mail->SMTPAuth = true;
    $mail->Username = 'bhuvanarajisri@gmail.com'; // Replace with your email
    $mail->Password = 'yajk vllc mbyr hqwq'; // Replace with your email password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Sender & Recipient
    $mail->setFrom('bhuvanarajisri@gmail.com', 'Cardiolume');
    $mail->addAddress($email);

    // Email Content
    $mail->isHTML(true);
    $mail->Subject = 'Your OTP for Password Recovery';
    $mail->Body = "Here is your OTP for password recovery: <strong>{$otp}</strong>";

    // Send Mail
    $mail->send();
    echo json_encode(["success" => "OTP sent successfully!", "email" => $email]);
} catch (Exception $e) {
    echo json_encode(["error" => "Email could not be sent. Mailer Error: {$mail->ErrorInfo}"]);
}
?>
