<?php
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "sample_db";

$tableName = $_GET['table'];

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SHOW COLUMNS FROM $tableName";
$result = $conn->query($sql);

$fields = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $fields[] = $row['Field'];
    }
}

echo json_encode($fields);

$conn->close();
?>
