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

$response = [
    "feedback" => 0,
    "university" => 0,
    "scholarships" => 0,
    "users" => 0
];

// Feedback count
$result = $conn->query("SELECT COUNT(*) AS count FROM contacts");
if ($result) {
    $response["feedback"] = (int)$result->fetch_assoc()['count'];
}

// University count
$result = $conn->query("SELECT COUNT(*) AS count FROM university");
if ($result) {
    $response["universities"] = (int)$result->fetch_assoc()['count'];
}

// Scholarships count
$result = $conn->query("SELECT COUNT(*) AS count FROM scholarships");
if ($result) {
    $response["scholarships"] = (int)$result->fetch_assoc()['count'];
}

// Users count (from another DB)
$result = $conn->query("SELECT COUNT(*) AS count FROM userdb.users");
if ($result) {
    $response["users"] = (int)$result->fetch_assoc()['count'];
}

echo json_encode($response);
$conn->close();
