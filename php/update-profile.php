<?php
session_start();
header('Content-Type: application/json');

// --- Database connection ---
$mysqli = new mysqli("localhost", "root", "", "userdb");
if ($mysqli->connect_errno) {
    echo json_encode(["success" => false, "message" => "Failed to connect to DB"]);
    exit;
}

// --- Check if user is logged in ---
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "You must be logged in"]);
    exit;
}

$userId = $_SESSION['user_id'];

// --- Get POST data ---
$firstName = trim($_POST['first_name'] ?? '');
$lastName  = trim($_POST['last_name'] ?? '');
$email     = trim($_POST['email'] ?? '');
$contact   = trim($_POST['contact'] ?? '');
$birthdate = trim($_POST['birthdate'] ?? '');
$password  = trim($_POST['password'] ?? '');

// --- Validate required fields ---
if (!$firstName || !$email) {
    echo json_encode(["success" => false, "message" => "First name and email are required"]);
    exit;
}

// --- Prepare password update if provided (plain text, no hash) ---
$updatePassword = '';
if (!empty($password)) {
    $updatePassword = ", password='$password'";
}

// --- Prepare SQL ---
$sql = "UPDATE users SET 
            firstname = ?, 
            last_name = ?, 
            email = ?, 
            contact_number = ?, 
            birthdate = ? $updatePassword
        WHERE id = ?";

$stmt = $mysqli->prepare($sql);
if (!$stmt) {
    echo json_encode(["success" => false, "message" => "DB prepare failed"]);
    exit;
}

// --- Bind parameters ---
$stmt->bind_param(
    "sssssi",
    $firstName,
    $lastName,
    $email,
    $contact,
    $birthdate,
    $userId
);

// --- Execute ---
if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Profile updated successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to update profile"]);
}

// --- Close ---
$stmt->close();
$mysqli->close();
?>
