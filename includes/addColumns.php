<?php
header('Content-Type: application/json');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "userData";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Database connection failed: " . $conn->connect_error]));
}

// Get the JSON input
$input = json_decode(file_get_contents('php://input'), true);
$userID = $input['userID'];
$columnName = $input['columnName'];

// Sanitize input
$userID = intval($userID);
$columnName = $conn->real_escape_string($columnName);

// Check if the column already exists for this user
$checkQuery = "SELECT COUNT(*) AS count FROM user_columns WHERE user_id = $userID AND column_name = '$columnName'";
$checkResult = $conn->query($checkQuery);
$checkRow = $checkResult->fetch_assoc();

if ($checkRow['count'] > 0) {
    echo json_encode(["success" => false, "message" => "Column already exists"]);
    exit;
}

// Insert the new column into the user_columns table
$insertQuery = "INSERT INTO user_columns (user_id, column_name, is_visible)  VALUES ($userID, '$columnName' , 1) ";
if ($conn->query($insertQuery) === true) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Error: " . $conn->error]);
}

$conn->close();
?>
