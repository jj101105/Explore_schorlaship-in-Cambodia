<?php
header('Content-Type: application/json');

$host = "localhost";
$user = "root";
$password = "";
$dbname = "scholarshipdb";

// Enable PHP error reporting (dev only)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Connect to DB
$conn = new mysqli($host, $user, $password, $dbname);

// Start session
session_start();

// TEMP: Auto-login admin for dev only
if (!isset($_SESSION['admin_logged_in'])) {
    $_SESSION['admin_logged_in'] = true;
}

// DB connection check
if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Connection failed: ' . $conn->connect_error]);
    exit();
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$response = ['status' => 'error', 'message' => 'Invalid action'];

if ($action === 'add') {
    $university_id = $_POST['university_id'] ?? '';
    $faculty_id = $_POST['faculty_id'] ?? '';
    $program_name = $_POST['program_name'] ?? '';
    $description = $_POST['description'] ?? '';

    if (empty($university_id) || empty($faculty_id) || empty($program_name)) {
        $response = ['status' => 'error', 'message' => 'University, Faculty, and Program Name are required.'];
    } else {
        $check = $conn->prepare("SELECT * FROM program WHERE university_id = ? AND faculty_id = ? AND LOWER(program_name) = LOWER(?)");
        $check->bind_param("iis", $university_id, $faculty_id, $program_name);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $response = ['status' => 'duplicate', 'message' => 'Program already exists for this university and faculty.'];
        } else {
            $stmt = $conn->prepare("INSERT INTO program (university_id, faculty_id, program_name, description) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiss", $university_id, $faculty_id, $program_name, $description);
            if ($stmt->execute()) {
                $response = ['status' => 'success', 'message' => 'Program added successfully!'];
            } else {
                $response = ['status' => 'error', 'message' => 'Error adding program: ' . $stmt->error];
            }
            $stmt->close();
        }
        $check->close();
    }
} elseif ($action === 'edit') {
    $program_id = $_POST['program_id'] ?? '';
    $university_id = $_POST['university_id'] ?? '';
    $faculty_id = $_POST['faculty_id'] ?? '';
    $program_name = $_POST['program_name'] ?? '';
    $description = $_POST['description'] ?? '';

    if (empty($program_id) || empty($university_id) || empty($faculty_id) || empty($program_name)) {
        $response = ['status' => 'error', 'message' => 'All fields are required for editing.'];
    } else {
        $stmt = $conn->prepare("UPDATE program SET university_id=?, faculty_id=?, program_name=?, description=? WHERE program_id=?");
        $stmt->bind_param("iissi", $university_id, $faculty_id, $program_name, $description, $program_id);
        if ($stmt->execute()) {
            $response = ['status' => 'success', 'message' => 'Program updated successfully!'];
        } else {
            $response = ['status' => 'error', 'message' => 'Error updating program: ' . $stmt->error];
        }
        $stmt->close();
    }
} elseif ($action === 'delete') {
    $program_id = $_POST['program_id'] ?? '';
    if (empty($program_id)) {
        $response = ['status' => 'error', 'message' => 'Program ID is required for deletion.'];
    } else {
        $stmt = $conn->prepare("DELETE FROM program WHERE program_id=?");
        $stmt->bind_param("i", $program_id);
        if ($stmt->execute()) {
            $response = ['status' => 'success', 'message' => 'Program deleted successfully!'];
        } else {
            $response = ['status' => 'error', 'message' => 'Error deleting program: ' . $stmt->error];
        }
        $stmt->close();
    }
} elseif ($action === 'fetch_universities') {
    $sql = "SELECT university_id, university_name FROM universities ORDER BY university_name ASC";
    $result = $conn->query($sql);
    $universities = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $universities[] = $row;
        }
    }
    echo json_encode($universities);
    $conn->close();
    exit();
} elseif ($action === 'fetch_faculties') {
    // This query now fetches ALL faculties
    $sql = "SELECT faculty_id, faculty_name FROM faculty ORDER BY faculty_name ASC";
    $result = $conn->query($sql);
    
    $faculties = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $faculties[] = $row;
        }
    }
    echo json_encode($faculties);
    $conn->close();
    exit();
} else {
    // Default: fetch all programs with university and faculty info
    $sql = "SELECT p.program_id, p.program_name, p.description, u.university_name, f.faculty_name
            FROM program p
            JOIN universities u ON p.university_id = u.university_id
            JOIN faculty f ON p.faculty_id = f.faculty_id
            ORDER BY u.university_name ASC, f.faculty_name ASC, p.program_name ASC";
    $result = $conn->query($sql);
    $programs = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $programs[] = $row;
        }
    }
    echo json_encode($programs);
    $conn->close();
    exit();
}

echo json_encode($response);
$conn->close();
?>