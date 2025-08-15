<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "You must be logged in"]);
    exit;
}

$userId = $_SESSION['user_id'];
$scholarshipId = intval($_POST['scholarship_id'] ?? 0);

if ($scholarshipId <= 0) {
    echo json_encode(["success" => false, "message" => "Invalid scholarship ID"]);
    exit;
}

$mysqli = new mysqli("localhost", "root", "", "userdb");
if ($mysqli->connect_errno) {
    echo json_encode(["success" => false, "message" => "DB connection failed"]);
    exit;
}

// Check if already in favorites
$check = $mysqli->prepare("SELECT id FROM user_favorites WHERE user_id=? AND scholarship_id=?");
$check->bind_param("ii", $userId, $scholarshipId);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    // Remove favorite
    $delete = $mysqli->prepare("DELETE FROM user_favorites WHERE user_id=? AND scholarship_id=?");
    $delete->bind_param("ii", $userId, $scholarshipId);
    $delete->execute();
    echo json_encode(["success" => true, "message" => "Removed from favorites", "favorited" => false]);
} else {
    // Add favorite
    $insert = $mysqli->prepare("INSERT INTO user_favorites (user_id, scholarship_id, created_at) VALUES (?, ?, NOW())");
    $insert->bind_param("ii", $userId, $scholarshipId);
    $insert->execute();
    echo json_encode(["success" => true, "message" => "Added to favorites", "favorited" => true]);
}

$check->close();
$mysqli->close();
?>
