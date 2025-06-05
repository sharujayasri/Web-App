<?php
session_start();
require '../vendor/autoload.php'; // Include PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format!";
        exit;
    }

    $otp = rand(100000, 999999); // Generate OTP
    $_SESSION['otp'] = $otp;
    $_SESSION['otp_expiry'] = time() + 300; // OTP expires in 5 minutes

    $mail = new PHPMailer(true);
    try {
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Replace with your SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'bhuvanarajisri@gmail.com'; // Replace with your email
        $mail->Password = 'yajk vllc mbyr hqwq'; // Use an App Password or SMTP password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Email Content
        $mail->setFrom('your_email@gmail.com', 'OTP Verification');
        $mail->addAddress($email);
        $mail->Subject = "Your OTP Code";
        $mail->Body = "Your OTP code is: $otp";
        
        $mail->send();
        echo "OTP sent successfully!";
    } catch (Exception $e) {
        echo "Failed to send OTP. Mailer Error: " . $mail->ErrorInfo;
    }
}
?>
