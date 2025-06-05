<?php
header('Content-Type: application/json');

// Step 1: Database connection
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$database = "cadiolume_db"; 

$conn = new mysqli($servername, $username, $password, $database);

// Step 2: Check connection
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed: " . $conn->connect_error]);
    exit;
}

// Step 3: Read JSON input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Step 4: Validate input
if ($data === null) {
    echo json_encode(["status" => "error", "message" => "Invalid JSON data received."]);
    exit;
}

// Step 5: Insert into database
$sql = "INSERT INTO user_details (
    male, age, education, currentSmoker, cigsPerDay, BPMeds, 
    prevalentStroke, prevalentHyp, diabetes, totChol, 
    sysBP, diaBP, BMI, heartRate, glucose
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(["status" => "error", "message" => "Failed to prepare SQL statement: " . $conn->error]);
    exit;
}

// Binding the parameters
$stmt->bind_param(
    "iiiiiiiiiiiiddi", 
    $data['male'], $data['age'], $data['education'], $data['currentSmoker'],
    $data['cigsPerDay'], $data['BPMeds'], $data['prevalentStroke'], $data['prevalentHyp'],
    $data['diabetes'], $data['totChol'], $data['sysBP'], $data['diaBP'],
    $data['BMI'], $data['heartRate'], $data['glucose']
);

// Executing the statement
if (!$stmt->execute()) {
    echo json_encode(["status" => "error", "message" => "Failed to insert data into database: " . $stmt->error]);
    exit;
}

// Step 6: Save input to CSV file for Python
$csv_file = 'test_input.csv'; // Make sure this is writable

$file = fopen($csv_file, 'w');
if ($file === false) {
    echo json_encode(["status" => "error", "message" => "Failed to open CSV file for writing."]);
    exit;
}

fputcsv($file, array_keys($data));   // Write headers
fputcsv($file, array_values($data)); // Write data
fclose($file);

// Step 7: Call Python script to predict
$python_script = 'app.py'; // make sure this file is inside the same folder or correct path

$output = shell_exec("python $python_script 2>&1"); // Capture both output and errors

// Step 8: Validate Python output
if ($output === null || trim($output) === "") {
    echo json_encode(["status" => "error", "message" => "Python script execution failed."]);
    exit;
}

// Step 9: Close DB connection
$conn->close();

// Step 10: Return success with Python output
echo json_encode([
    "status" => "success",
    "python_output" => $output
]);
?>