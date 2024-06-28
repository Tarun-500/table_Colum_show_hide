<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dynamic_table";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$userId = intval($_POST['userId']);
$columnName = $_POST['columnName'];

$sql = "SELECT COUNT(*) AS count FROM user_columns WHERE user_id = $userId AND column_name = '$columnName'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

if ($row['count'] > 0) {
    echo 'exists';
} else {
    $sql = "SELECT MAX(column_index) AS max_index FROM user_columns WHERE user_id = $userId";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $newIndex = $row['max_index'] + 1;

    if ($newIndex <= 20) {
        $sql = "INSERT INTO user_columns (user_id, column_name, column_index) VALUES ($userId, '$columnName', $newIndex)";
        if ($conn->query($sql) === TRUE) {
            echo 'success';
        } else {
            echo 'error';
        }
    } else {
        echo 'limit';
    }
}

$conn->close();
?>
