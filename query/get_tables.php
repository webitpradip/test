<?php
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "sample_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SHOW TABLES";
$result = $conn->query($sql);

$tables = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $tables[] = $row["Tables_in_$dbname"];
    }
}

echo json_encode($tables);

$conn->close();
?>
