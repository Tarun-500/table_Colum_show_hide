<?php

header('Content-Type: application/json');
include '../db/db.php';

// Get the request body
$requestBody = json_decode(file_get_contents('php://input'), true);

if (!isset($requestBody['userID']) || !isset($requestBody['columnName']) || !isset($requestBody['isVisible'])) {
    echo json_encode(["error" => "Required parameter(s) missing from request."]);
    exit;
}

$userID = $requestBody['userID'];
$columnName = $requestBody['columnName'];
$isVisible = $requestBody['isVisible'] ? 1 : 0;

$query = "UPDATE user_columns SET is_visible = ? WHERE user_id = ? AND column_name = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('isi', $isVisible, $userID, $columnName);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to update column visibility.']);
}

$stmt->close();
$conn->close();