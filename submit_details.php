<?php
// Only allow POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["status" => "error", "message" => "Only POST requests are allowed."]);
    exit();
}

// Read the JSON body
$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true);

if (!$data) {
    echo json_encode(["status" => "error", "message" => "Invalid JSON input."]);
    exit();
}

// Extract and validate data
$requiredFields = ['age', 'weight', 'height', 'profession', 'gender', 'smoking', 'smoking_count', 'hypertension', 'bp_medicine', 'diabetes', 'prevalent_stroke', 'cholesterol'];
foreach ($requiredFields as $field) {
    if (empty($data[$field])) {
        echo json_encode(["status" => "error", "message" => "Missing field: $field"]);
        exit();
    }
}

// Assign values
$age = $data['age'];
$weight = $data['weight'];
$height = $data['height'];
$profession = $data['profession'];
$gender = $data['gender'];
$smoking = $data['smoking'];
$smoking_count = $data['smoking_count'];
$hypertension = $data['hypertension'];
$bp_medicine = $data['bp_medicine'];
$diabetes = $data['diabetes'];
$prevalent_stroke = $data['prevalent_stroke'];
$cholesterol = $data['cholesterol'];

// Save to CSV
$file_path = "user_data.csv";
$file = fopen($file_path, "a");

if (!$file) {
    echo json_encode(["status" => "error", "message" => "Unable to open file for writing."]);
    exit();
}

fputcsv($file, [$age, $weight, $height, $profession, $gender, $smoking, $smoking_count, $hypertension, $bp_medicine, $diabetes, $prevalent_stroke, $cholesterol]);
fclose($file);

// Respond with success
echo json_encode(["status" => "success", "message" => "Details saved successfully."]);
exit();
?>
