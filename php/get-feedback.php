<?php
// Database credentials (update as needed)
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'scholarshipdb'; // Change this to your actual DB name

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit;
}

// Query the feedback table
$sql = "SELECT id, name, email, message, submitted_at FROM contacts ORDER BY submitted_at DESC";
$result = $conn->query($sql);

// Store results in an array
$feedback = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $feedback[] = $row;
    }
}

// Output JSON
header('Content-Type: application/json');
echo json_encode($feedback);

// Close connection
$conn->close();
?>
