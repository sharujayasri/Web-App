<?php
session_start();

// Debugging: Check session values
if (!isset($_SESSION['otp'])) {
    echo "❌ No OTP found. Please request a new one.";
    exit;
}

if (!isset($_SESSION['otp_expiry'])) {
    echo "❌ OTP Expiry not set. Please request a new one.";
    exit;
}

// Debugging: Show stored and entered OTP for verification
$entered_otp = $_POST['otp'] ?? '';
$stored_otp = $_SESSION['otp'];
$otp_expiry = $_SESSION['otp_expiry'];

if (time() > $otp_expiry) {
    echo "❌ OTP expired. Please request a new one.";
    unset($_SESSION['otp']); 
    unset($_SESSION['otp_expiry']);
    exit;
}

// Convert to the same data type for comparison
if ((string)$entered_otp === (string)$stored_otp) { 
    echo "✅ OTP Verified Successfully!";
    unset($_SESSION['otp']); // Remove OTP after verification
    unset($_SESSION['otp_expiry']);
} else {
    echo "❌ Invalid OTP!";
}
?>
