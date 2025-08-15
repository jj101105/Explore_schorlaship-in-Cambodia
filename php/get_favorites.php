<?php
session_start();
header('Content-Type: application/json');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "You must be logged in"]);
    exit;
}

$userId = $_SESSION['user_id'];

// Step 1: Get scholarship IDs from userdb
$mysqliUser = new mysqli("localhost", "root", "", "userdb");
if ($mysqliUser->connect_errno) {
    echo json_encode(["success" => false, "message" => "User DB connection failed"]);
    exit;
}

$favQuery = $mysqliUser->prepare("SELECT scholarship_id FROM user_favorites WHERE user_id=?");
$favQuery->bind_param("i", $userId);
$favQuery->execute();
$result = $favQuery->get_result();

$scholarshipIds = [];
while ($row = $result->fetch_assoc()) {
    $scholarshipIds[] = $row['scholarship_id'];
}
$mysqliUser->close();

// Step 2: If no favorites, return empty list
if (empty($scholarshipIds)) {
    echo json_encode(["success" => true, "favorites" => []]);
    exit;
}

// Step 3: Fetch scholarship details from scholarshipdb
$mysqliScholar = new mysqli("localhost", "root", "", "scholarshipdb");
if ($mysqliScholar->connect_errno) {
    echo json_encode(["success" => false, "message" => "Scholarship DB connection failed"]);
    exit;
}

$placeholders = implode(',', array_fill(0, count($scholarshipIds), '?'));
$types = str_repeat('i', count($scholarshipIds));

$sql = "
    SELECT 
        id,
        title,
        subtitle,
        JSON_UNQUOTE(JSON_EXTRACT(details, '$.university')) AS university,
        JSON_UNQUOTE(JSON_EXTRACT(details, '$.type')) AS type
    FROM scholarships
    WHERE id IN ($placeholders)
";

$stmt = $mysqliScholar->prepare($sql);
$stmt->bind_param($types, ...$scholarshipIds);
$stmt->execute();
$result = $stmt->get_result();

$favorites = [];
while ($row = $result->fetch_assoc()) {
    $favorites[] = $row;
}

$stmt->close();
$mysqliScholar->close();

// Step 4: Return as JSON
echo json_encode(["success" => true, "favorites" => $favorites]);
?>
