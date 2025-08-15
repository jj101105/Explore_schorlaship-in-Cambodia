<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    die("Login required");
}

$userId = $_SESSION['user_id'];

// Get favorites from userdb
$mysqliUser = new mysqli("localhost", "root", "", "userdb");
$favQuery = $mysqliUser->prepare("SELECT scholarship_id FROM user_favorites WHERE user_id=?");
$favQuery->bind_param("i", $userId);
$favQuery->execute();
$result = $favQuery->get_result();
$scholarshipIds = [];
while ($row = $result->fetch_assoc()) {
    $scholarshipIds[] = $row['scholarship_id'];
}
$mysqliUser->close();

$scholarships = [];
if (!empty($scholarshipIds)) {
    // Fetch details from scholarshipdb
    $mysqliScholar = new mysqli("localhost", "root", "", "scholarshipdb");
    $placeholders = implode(',', array_fill(0, count($scholarshipIds), '?'));
    $types = str_repeat('i', count($scholarshipIds));
    $sql = "SELECT * FROM scholarships WHERE id IN ($placeholders)";
    $stmt = $mysqliScholar->prepare($sql);
    $stmt->bind_param($types, ...$scholarshipIds);
    $stmt->execute();
    $scholarships = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $mysqliScholar->close();
}
?>
