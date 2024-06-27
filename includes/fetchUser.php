<?php
header('Content-Type: application/json');

include_once '../db/db.php';

$sql = "SELECT name, nickname, mobile, email, role, address, gender, profile_image FROM users";
$result = $conn->query($sql);

$users = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}
$conn->close();

echo json_encode($users);
?>
