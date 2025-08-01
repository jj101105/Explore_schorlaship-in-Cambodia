<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
    exit();
}

$mysqli = new mysqli("localhost", "root", "", "scholarshipdb");
if ($mysqli->connect_errno) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed"]);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
if (!$data || !isset($data['id'])) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid input, ID required"]);
    exit();
}

$stmt = $mysqli->prepare("DELETE FROM scholarships1 WHERE id=?");
$stmt->bind_param("i", $data['id']);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Scholarship deleted"]);
} else {
    http_response_code(500);
    echo json_encode(["error" => $stmt->error]);
}

$stmt->close();
$mysqli->close();
?>
