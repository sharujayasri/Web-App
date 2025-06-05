<?php
session_start(); // Start the session
include 'db_config.php'; // Ensure database connection file exists
ini_set('display_errors', 1);
error_reporting(E_ALL);


// CORS Handling
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Check database connection
if (!$conn) {
    echo json_encode(["status" => "error", "message" => "Database connection failed."]);
    exit;
}

// Read and decode JSON input
$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true);

if (!$data) {
    echo json_encode(["status" => "error", "message" => "Invalid JSON data."]);
    exit;
}

// Retrieve input values
$username = trim($data['username'] ?? '');
$mobile = trim($data['mobile'] ?? '');
$email = trim($data['email'] ?? '');
$password = trim($data['password'] ?? '');

// Input validation
if (empty($username) || empty($mobile) || empty($email) || empty($password)) {
    echo json_encode(["status" => "error", "message" => "All fields are required."]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["status" => "error", "message" => "Invalid email format."]);
    exit;
}

if (!preg_match('/^\d{10}$/', $mobile)) {
    echo json_encode(["status" => "error", "message" => "Invalid phone number."]);
    exit;
}

// Hash password for security
// $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Check if user already exists
$stmt = $conn->prepare("SELECT id FROM user WHERE email = ? OR mobile = ?");
$stmt->bind_param("ss", $email, $mobile);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "User already exists."]);
    $stmt->close();
    $conn->close();
    exit;
}

// Insert user into database
$stmt = $conn->prepare("INSERT INTO user (username, mobile, email, password) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $username, $mobile, $email, $password);

if ($stmt->execute()) {
    $_SESSION['user_id'] = $stmt->insert_id; // Store user_id in session
    echo json_encode(["status" => "success", "message" => "Registration successful.", "user_id" => $_SESSION['user_id']]);
} else {
    echo json_encode(["status" => "error", "message" => "Registration failed."]);
}

$stmt->close();
$conn->close();
?>
