<?php
include '../Backend/db_config.php'; // Adjust path to database connection if needed

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = $_POST['new_password'];
    $email = $_POST['email']; // Get email from form input

    // Perform password validation (length, strength, etc.)
    if (strlen($new_password) < 6) {
        echo "<p style='color:red;'>Password must be at least 6 characters long.</p>";
        exit;
    }

    // Hash the new password before storing
    // $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

    // Update password in the database
    $sql = "UPDATE user SET password = ? WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $new_password, $email);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        echo "<p style='color:green;'>Password successfully reset.</p>";
    } else {
        echo "<p style='color:red;'>Failed to reset password. Please check the email and try again.</p>";
    }

    $stmt->close();
    $conn->close();
}
?>
