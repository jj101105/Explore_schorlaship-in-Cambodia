<?php
// Connect to DB
$conn = new mysqli("localhost", "root", "", "scholarshipdb");
$data = json_decode(file_get_contents("php://input"));

$user_id = $conn->real_escape_string($data->user_id);
$scholarship_id = $conn->real_escape_string($data->scholarship_id);

// Check if already favorited
$check = $conn->query("SELECT * FROM favorites WHERE user_id = '$user_id' AND scholarship_id = '$scholarship_id'");
if ($check->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "Already added"]);
    exit;
}

// Insert new favorite
$conn->query("INSERT INTO favorites (user_id, scholarship_id) VALUES ('$user_id', '$scholarship_id')");
echo json_encode(["success" => true]);
?>
