<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'userdb';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit;
}
$conn->set_charset("utf8");

// Handle POST: Add new user
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = $_POST['first_name'] ?? '';
    $lastname = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['contact_number'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($firstname && $lastname && $email && $phone && $password) {
        $stmt = $conn->prepare("INSERT INTO users (firstname, last_name, email, contact_number, password, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("sssss", $firstname, $lastname, $email, $phone, $password);

        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(["success" => false, "error" => "All fields are required."]);
    }

    $conn->close();
    exit;
}

// Handle GET: Fetch all users
$sql = "SELECT id, firstname, last_name AS lastname, email, contact_number AS phone, password, created_at FROM users ORDER BY id ASC";
$result = $conn->query($sql);

$users = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

echo json_encode($users);
$conn->close();
?>
