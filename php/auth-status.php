<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["loggedIn" => false]);
    exit();
}

// --- Database credentials ---
$host = "localhost";
$user = "root";
$password = "";

// Connect to userdb
$userdb = new mysqli($host, $user, $password, "userdb");
if ($userdb->connect_error) {
    echo json_encode(['loggedIn' => false, 'error' => 'User DB connection failed']);
    exit();
}

// Connect to scholarshipdb (optional, for later use)
$scholardb = new mysqli($host, $user, $password, "scholarshipdb");
if ($scholardb->connect_error) {
    echo json_encode(['loggedIn' => false, 'error' => 'Scholarship DB connection failed']);
    exit();
}

// Fetch user info from userdb
$user_id = $_SESSION['user_id'];
$stmt = $userdb->prepare("SELECT firstname, last_name, email, contact_number, birthdate FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['loggedIn' => false, 'error' => 'User not found']);
    exit();
}

$user = $result->fetch_assoc();

// Example: later you can fetch wishlist from scholarshipdb
/*
$wishStmt = $scholardb->prepare("SELECT scholarship_name FROM wishlist WHERE user_id = ?");
$wishStmt->bind_param("i", $user_id);
$wishStmt->execute();
$wishResult = $wishStmt->get_result();
$wishlist = [];
while ($row = $wishResult->fetch_assoc()) {
    $wishlist[] = $row['scholarship_name'];
}
*/

echo json_encode([
    "loggedIn"   => true,
    "first_name" => $user['firstname'],
    "last_name"  => $user['last_name'],
    "email"      => $user['email'],
    "contact"    => $user['contact_number'],
    "birthdate"  => $user['birthdate'],
    // "wishlist" => $wishlist // optional
]);

$stmt->close();
$userdb->close();
$scholardb->close();
?>
