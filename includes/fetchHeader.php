<?php

header('Content-Type: application/json');
include '../db/db.php';

if (isset($_GET['userID'])) {
    $userID = $_GET['userID'];
} else {
    echo json_encode(["error" => "User ID not provided in the URL."]);
    exit;
}

$columnsQuery = "SELECT column_name, is_visible FROM user_columns WHERE user_id = ?";
$columnsStmt = $conn->prepare($columnsQuery);
$columnsStmt->bind_param('i', $userID);
$columnsStmt->execute();
$columnsResult = $columnsStmt->get_result();

$columns = [];
while ($row = $columnsResult->fetch_assoc()) {
    $columnName = $row['column_name'];
    $isVisible = $row['is_visible'];
    if ($isVisible) {
        $columns[] = ['name' => $columnName];
    }
}

$columnsStmt->close();
$conn->close();

echo json_encode(['columns' => $columns]);