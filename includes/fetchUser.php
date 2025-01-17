<?php

header('Content-Type: application/json');
include '../db/db.php';

if (isset($_GET['userID'])) {
    $userID = $_GET['userID'];
} else {
    echo json_encode(["error" => "User ID not provided in the URL."]);
    exit;
}

$query = "SELECT * FROM users";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

$users = [];
while ($row = $result->fetch_assoc()) {
    $users[$row['id']] = [
        'id' => $row['id'],
        'name' => $row['name'],
        'nickname' => $row['nickname'],
        'mobile' => $row['mobile'],
        'email' => $row['email'],
        'role' => $row['role'],
        'address' => $row['address'],
        'gender' => $row['gender'],
        'profile_image' => $row['profile_image'],
        'custom_columns' => []
    ];
}

$columnsQuery = "SELECT user_id, column_name, is_visible FROM user_columns WHERE user_id = ?";
$columnsStmt = $conn->prepare($columnsQuery);
$columnsStmt->bind_param('i', $userID);
$columnsStmt->execute();
$columnsResult = $columnsStmt->get_result();

$columns = [];
while ($row = $columnsResult->fetch_assoc()) {
    $userId = $row['user_id'];
    $columnName = $row['column_name'];
    $isVisible = $row['is_visible'];
    if (isset($users[$userId]) && $isVisible) {
        $users[$userId]['custom_columns'][] = ['name' => $columnName];
    }
    if ($isVisible) {
        $columns[] = ['name' => $columnName];
    }
}

$stmt->close();
$columnsStmt->close();
$conn->close();

$users = array_values($users);

echo json_encode(['users' => $users, 'columns' => $columns]);
