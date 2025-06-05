<?php
header("Content-Type: application/json"); // Set response type as JSON
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db_config.php'; // Include database connection

// Read and decode JSON input
$data = json_decode(file_get_contents("php://input"), true);

// Enable debugging (set to false in production)
$debug = false;
if ($debug) {
    file_put_contents("debug.txt", json_encode($data, JSON_PRETTY_PRINT));
}

// Validate JSON input
if (!$data || !isset($data["loginInput"]) || !isset($data["password"])) {
    echo json_encode(["status" => "error", "message" => "Invalid JSON data."]);
    exit;
}

$loginInput = trim($data["loginInput"]);
$password = trim($data["password"]);

// Check for empty values
if (empty($loginInput) || empty($password)) {
    echo json_encode(["status" => "error", "message" => "All fields are required."]);
    exit;
}

// Prepare SQL query to check if user exists (using prepared statements)
$stmt = $conn->prepare("SELECT id, username, email, mobile, password FROM user WHERE email = ? OR mobile = ?");
$stmt->bind_param("ss", $loginInput, $loginInput);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "User not found."]);
    exit;
}

// Fetch user data
$user = $result->fetch_assoc();

// Normal password check (without hashing)
if ($password !== $user["password"]) {
    echo json_encode(["status" => "error", "message" => "Incorrect password."]);
    exit;
}

// Start user session after successful login
$_SESSION["user_id"] = $user["id"];
$_SESSION["username"] = $user["username"];
$_SESSION["email"] = $user["email"];
$_SESSION["mobile"] = $user["mobile"];

// Success response
echo json_encode([
    "status" => "success",
    "message" => "Login successful.",
    "user" => [
        "id" => $user["id"],
        "username" => $user["username"],
        "email" => $user["email"],
        "mobile" => $user["mobile"]
    ]
]);

$stmt->close();
$conn->close();
?>
