<?php
header('Content-Type: application/json');

$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'scholarshipdb';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit();
}

$feedbackCount = 0;
$result = $conn->query("SELECT COUNT(*) AS count FROM contacts");
if ($result) {
    $feedbackCount = $result->fetch_assoc()['count'];
}

echo json_encode(["feedback" => $feedbackCount]);

$conn->close();
