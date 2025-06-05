<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            width: 350px;
        }
        h2 {
            text-align: center;
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            width: 100%;
            padding: 10px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background: #218838;
        }
        .message {
            text-align: center;
            color: red;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Reset Password</h2>
        <form id="resetForm">
            <label for="email">Email:</label>
            <input type="email" id="email" required>

            <label for="new_password">New Password:</label>
            <input type="password" id="new_password" required>

            <button type="submit">Reset Password</button>
        </form>
        <p id="message" class="message"></p>
    </div>

    <script>
        $(document).ready(function() {
            $("#resetForm").submit(function(event) {
                event.preventDefault();

                let email = $("#email").val();
                let new_password = $("#new_password").val();
                let message = $("#message");

                if (new_password.length < 6) {
                    message.css("color", "red").text("Password must be at least 6 characters long.");
                    return;
                }

                $.ajax({
                    url: "../Backend/reset_password.php",
                    type: "POST",
                    data: {
                        email: email,
                        new_password: new_password
                    },
                    success: function(response) {
                        message.html(response);
                        if (response.includes("successfully")) {
                            message.css("color", "green");
                            window.locatiion.href="log.html";
                        } else {
                            message.css("color", "red");
                        }
                    },
                    error: function() {
                        message.css("color", "red").text("Error resetting password. Please try again.");
                    }
                });
            });
        });
    </script>

</body>
</html>
