<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "scholarshipdb";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set timezone and created_at value
date_default_timezone_set("Asia/Phnom_Penh");
$created_at = date("Y-m-d H:i:s");

// Get action from POST
$action = $_POST['action'] ?? '';

// Common inputs (sanitize as needed)
$name = $_POST['scholarship_name'] ?? '';
$province = $_POST['province'] ?? '';
$gpa = isset($_POST['gpa']) ? floatval($_POST['gpa']) : 0;
$degree = $_POST['degree_level'] ?? '';
$type = $_POST['scholarship_type'] ?? '';
$fields = $_POST['fields_of_study'] ?? '';
$collegeType = $_POST['college_type'] ?? '';
$description = $_POST['description'] ?? '';

if ($action === 'add') {
    $sql = "INSERT INTO scholarships (name, province, gpa, degree_level, type, fields_of_study, college_type, description, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdssssss", $name, $province, $gpa, $degree, $type, $fields, $collegeType, $description, $created_at);

    if ($stmt->execute()) {
    echo "added"; // ✅ Match the JavaScript condition
    }else {
        echo "error: " . $stmt->error;
    }
} 
elseif ($action === 'edit') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    if ($id <= 0) {
        echo "Invalid ID";
        exit;
    }

    $sql = "UPDATE scholarships SET name=?, province=?, gpa=?, degree_level=?, type=?, fields_of_study=?, college_type=?, description=?
            WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdsssssi", $name, $province, $gpa, $degree, $type, $fields, $collegeType, $description, $id);

    if ($stmt->execute()) {
        echo "edited";
    } else {
        echo "error: " . $stmt->error;
    }
}
elseif ($action === 'delete') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    if ($id <= 0) {
        echo "Invalid ID";
        exit;
    }

    $sql = "DELETE FROM scholarships WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "deleted";
    } else {
        echo "error: " . $stmt->error;
    }
} 
else {
    echo "invalid action";
}

$conn->close();
?>
