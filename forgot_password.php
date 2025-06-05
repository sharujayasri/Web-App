<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h2>Forgot Password</h2>
    <p id="message"></p>

    <form id="forgotForm">
        <label>Email:</label>
        <input type="email" name="email" id="email" required>
        <button type="submit">Send OTP</button>
    </form>

    <script>
        $(document).ready(function() {
            $("#forgotForm").submit(function(e) {
                e.preventDefault(); // Prevent form from reloading the page

                var email = $("#email").val();

                $.ajax({
                    url: "../Backend/forget.php",
                    type: "POST",
                    data: { email: email },
                    dataType: "json",
                    success: function(response) {
                        $("#message").text(response.message);
                        if (response.success) {
                            setTimeout(function() {
                                window.location.href = "../Frontend/verify_otp.php"; 
                            }, 2000); // Redirect after 2 seconds if successful
                        }
                    },
                    error: function() {
                        $("#message").text("An error occurred. Please try again.");
                    }
                });
            });
        });
    </script>
</body>
</html>
