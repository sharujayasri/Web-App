<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
</head>
<body>
    <h2>Forgot Password</h2>
    <?php if (isset($_SESSION['message'])) { echo "<p>" . $_SESSION['message'] . "</p>"; unset($_SESSION['message']); } ?>
    <form method="POST" action="../Backend/forget.php">
        <label>Email:</label>
        <input type="email" name="email" required>
        <button type="submit">Send OTP</button>
    </form>
</body>
</html>
