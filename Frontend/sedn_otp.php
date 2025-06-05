<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 20px; }
        input { padding: 10px; margin: 10px; width: 80%; max-width: 300px; }
        button { padding: 10px; cursor: pointer; }
    </style>
</head>
<body>
    <h2>Enter Your Email to Receive OTP</h2>
    <input type="email" id="email" placeholder="Enter your email" required>
    <button id="sendOtpBtn">Send OTP</button>
    <p id="otpMessage"></p>

    <script>
        $(document).ready(function() {
            $("#sendOtpBtn").click(function() {
                let email = $("#email").val();

                if (email === "") {
                    alert("Please enter a valid email");
                    return;
                }

                $.ajax({
                    type: "POST",
                    url: "../Backend/send_otp.php",
                    data: { email: email },
                    success: function(response) {
                    alert(response);  // Show success message
                    if (response.includes("OTP sent successfully")) {  
                        window.location.href = "verify_otp.php"; // Redirect to OTP verification page
                    }
                }
                });
            });
        });
    </script>
</body>
</html>
