<?php
header('Content-Type: application/json');
include_once '../db/db.php';

$data = json_decode(file_get_contents("php://input"), true);

$userID = $data['userID'];
$columnName = $data['columnName'];

$query = "DELETE FROM user_columns WHERE user_id = ? AND column_name = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("is", $userID, $columnName);

$response = [];
if ($stmt->execute()) {
    $response['success'] = true;
} else {
    $response['success'] = false;
    $response['error'] = $stmt->error;
}

$stmt->close();
$conn->close();

echo json_encode($response);
 
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
    $id = $row['id'];
    $columnName = $row['column_name'];
    $isVisible = $row['is_visible'];
    $columnsQueryInner = "SELECT * FROM custom_column_value WHERE user_id = ? AND user_column_id = ?";

    $columnsStmtInner = $conn->prepare($columnsQueryInner);
    $columnsStmtInner->bind_param('ii', $userId, $id);
    $columnsStmtInner->execute();
    $columnsResultInner = $columnsStmtInner->get_result();
    while ($rowInner = $columnsResultInner->fetch_assoc()) {
        $columnName = $rowInner['value'];

        if (isset($users[$userId]) && $isVisible) {
            $users[$userId]['custom_columns'][] = ['name' => $columnName];
        }
        if ($isVisible) {
            $columns[] = ['name' => $columnName];
        }
    }
}

$stmt->close();
$columnsStmt->close();
$conn->close();

$users = array_values($users);

echo json_encode(['users' => $users, 'columns' => $columns]);
