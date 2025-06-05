<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "cadiolume_db";

// Create connection
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["error" => "Invalid or empty JSON data"]);
    exit;
}

// Validate all expected fields exist
$requiredFields = ['age', 'weight', 'height', 'profession', 'gender', 'smoking', 'smoking_count', 'hypertension', 'bp_medicine', 'diabetes', 'prevalent_stroke', 'cholesterol'];

foreach ($requiredFields as $field) {
    if (!isset($data[$field])) {
        echo json_encode(["error" => "Missing field: $field"]);
        exit;
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

// Prepare SQL
$sql = "INSERT INTO fill_detail 
(age, weight, height, profession, gender, smoking, smoking_count, hypertension, bp_medicine, diabetes, prevalent_stroke, cholesterol)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param(
        "iiisssisssss",
        $age,
        $weight,
        $height,
        $profession,
        $gender,
        $smoking,
        $smoking_count,
        $hypertension,
        $bp_medicine,
        $diabetes,
        $prevalent_stroke,
        $cholesterol
    );

    if ($stmt->execute()) {
        echo json_encode(["message" => "Form submitted successfully!"]);
    } else {
        echo json_encode(["error" => "Execution failed: " . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(["error" => "Prepare failed: " . $conn->error]);
}

$conn->close();
?>
