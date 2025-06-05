<?php
header('Content-Type: application/json');

// Step 1: Database connection
$host = "localhost";
$user = "root";
$password = "";
$database = "cadiolume_db";

$conn = new mysqli($host, $user, $password, $database);

// Step 2: Check connection
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed: " . $conn->connect_error]);
    exit;
}

// Step 3: Query to fetch all data
$sql = "SELECT * FROM user_details"; // ✅ Correct table name
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Step 4: Open the CSV file for writing
    $file_path = 'exported_user_details.csv'; // The CSV file will be created here
    $file = fopen($file_path, 'w');

    if (!$file) {
        echo json_encode(["status" => "error", "message" => "Failed to open file for writing."]);
        exit;
    }

    // Step 5: Write column headers
    $fields = $result->fetch_fields();
    $headers = [];
    foreach ($fields as $field) {
        $headers[] = $field->name;
    }
    fputcsv($file, $headers);

    // Step 6: Write each row
    while ($row = $result->fetch_assoc()) {
        fputcsv($file, $row);
    }

    fclose($file);

    echo json_encode(["status" => "success", "message" => "Data exported successfully to $file_path"]);
} else {
    echo json_encode(["status" => "error", "message" => "No data found in the table."]);
}

$conn->close();
?>
