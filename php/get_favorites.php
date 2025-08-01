<?php
$conn = new mysqli("localhost", "root", "", "scholarshipdb");
$user_id = $_GET['user_id'];

// Get all favorite scholarship IDs
$result = $conn->query("SELECT scholarship_id FROM favorites WHERE user_id = '$user_id'");
$favorites = [];

while ($row = $result->fetch_assoc()) {
    $favorites[] = $row['scholarship_id'];
}

// You might fetch full data from another table or use a JSON file
echo json_encode($favorites);
?>
