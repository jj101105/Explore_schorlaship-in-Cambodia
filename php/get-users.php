<?php
header('Content-Type: application/json');

$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'userdb'; // Your actual DB name

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit;
}

$sql = "SELECT id, firstname, email, created_at FROM users ORDER BY created_at DESC";
$result = $conn->query($sql);

$users = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

echo json_encode($users);
$conn->close();
?>
