<?php
session_start(); // Start the session

// Check if user is logged in (make sure user ID is stored in the session)
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if user is not authenticated
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

include('db_config.php');

// Fetch user data from the database based on user ID
$sql = "SELECT username, email, profile FROM user WHERE id = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    // If statement preparation fails
    die("Error preparing the query: " . $conn->error);
}

$stmt->bind_param("i", $user_id); // Bind user ID as an integer parameter
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Fetch user data
    $user = $result->fetch_assoc();
    // Store fetched data into session variables
    $_SESSION['user']['username'] = $user['username'];
    $_SESSION['user']['email'] = $user['email'];
    $_SESSION['user']['profile'] = $user['profile'];
} else {
    // If no user data found, redirect to login page
    header("Location: login.php");
    exit();
}

// Assign session values for easy use in the HTML
$name = $_SESSION['user']['username'];
$email = $_SESSION['user']['email'];
$profile_pic = $_SESSION['user']['profile'];
?>
