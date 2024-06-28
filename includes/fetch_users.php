<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dynamic_table";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$userId = isset($_GET['userId']) ? intval($_GET['userId']) : 0;

$sql = "SELECT * FROM users";
$result = $conn->query($sql);

$users = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

$customColumns = array();
if ($userId > 0) {
    $sql = "SELECT column_name, column_index FROM user_columns WHERE user_id = $userId ORDER BY column_index";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $customColumns[] = $row;
        }
    }
}

$conn->close();

echo json_encode(array('users' => $users, 'customColumns' => $customColumns));
?>
