<?php
session_start();
require_once "../Backend/db_config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $entered_otp = trim($_POST['otp']);

    if (!isset($_SESSION['reset_email']) || !isset($_SESSION['reset_otp'])) {
        $_SESSION['message'] = "Invalid session. Please try again.";
        header("Location: forgot_password.php");
        exit();
    }

    $email = $_SESSION['reset_email'];
    $stored_otp = $_SESSION['reset_otp'];
    $otp_expiry = $_SESSION['otp_expiry'];

    if (time() > $otp_expiry) {
        $_SESSION['message'] = "OTP has expired.";
        header("Location: forgot_password.php");
        exit();
    }

    if ($entered_otp == $stored_otp) {
        $_SESSION['otp_verified'] = true;
        header("Location: reset_password.php");
        exit();
    } else {
        $_SESSION['message'] = "Invalid OTP. Please try again.";
        header("Location: verify_otp.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
</head>
<body>
    <h2>Enter OTP</h2>
    <?php if (isset($_SESSION['message'])) { echo "<p>" . $_SESSION['message'] . "</p>"; unset($_SESSION['message']); } ?>
    <form method="POST" action="">
        <label>OTP:</label>
        <input type="text" name="otp" required>
        <button type="submit">Verify OTP</button>
    </form>
</body>
</html>
