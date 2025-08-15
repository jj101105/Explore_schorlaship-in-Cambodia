<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost';
$username = 'root';
$password = '';
$scholarshipDb = 'scholarshipdb';
$userDb = 'userdb';

$conn = new mysqli($host, $username, $password, $scholarshipDb);
if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit();
}

$response = ["feedback"=>0,"universities"=>0,"scholarships"=>0,"users"=>0];

// Feedback count
$result = $conn->query("SELECT COUNT(*) AS count FROM contacts") or die($conn->error);
$response["feedback"] = (int)$result->fetch_assoc()['count'];

// University count
$result = $conn->query("SELECT COUNT(*) AS count FROM universities"); // <-- use the correct table name
if ($result) {
    $response["universities"] = (int)$result->fetch_assoc()['count'];
}

// Scholarships count
$result = $conn->query("SELECT COUNT(*) AS count FROM scholarships") or die($conn->error);
$response["scholarships"] = (int)$result->fetch_assoc()['count'];

// Users count (switch db)
$conn->select_db($userDb) or die($conn->error);
$result = $conn->query("SELECT COUNT(*) AS count FROM users") or die($conn->error);
$response["users"] = (int)$result->fetch_assoc()['count'];

echo json_encode($response);
$conn->close();
