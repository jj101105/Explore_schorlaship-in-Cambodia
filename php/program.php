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

// --- DEV ONLY: TEMPORARY AUTO-LOGIN FOR ADMIN (REMOVE IN PRODUCTION) ---
if (!isset($_SESSION['admin_logged_in'])) {
    $_SESSION['admin_logged_in'] = true;
}

// --- DB CONNECTION ERROR CHECK ---
if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Connection failed: ' . $conn->connect_error]);
    exit();
}

// --- HANDLE REQUEST ---
$action = $_POST['action'] ?? $_GET['action'] ?? '';
$response = ['status' => 'error', 'message' => 'Invalid action'];

if ($action === 'add') {
    $university_id = $_POST['university_id'] ?? '';
    $program_name = $_POST['program_name'] ?? '';
    $description = $_POST['description'] ?? '';

    if (empty($university_id) || empty($program_name)) {
        $response = ['status' => 'error', 'message' => 'University and Program Name are required.'];
    } else {
        $check = $conn->prepare("SELECT * FROM program WHERE University_ID = ? AND LOWER(Program_Name) = LOWER(?)");
        $check->bind_param("is", $university_id, $program_name);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $response = ['status' => 'duplicate', 'message' => 'Program already exists for this university.'];
        } else {
            $stmt = $conn->prepare("INSERT INTO program (University_ID, Program_Name, Description) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $university_id, $program_name, $description);
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
    $program_name = $_POST['program_name'] ?? '';
    $description = $_POST['description'] ?? '';

    if (empty($program_id) || empty($university_id) || empty($program_name)) {
        $response = ['status' => 'error', 'message' => 'All fields are required for editing.'];
    } else {
        $stmt = $conn->prepare("UPDATE program SET University_ID=?, Program_Name=?, Description=? WHERE Program_ID=?");
        $stmt->bind_param("issi", $university_id, $program_name, $description, $program_id);
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
        $stmt = $conn->prepare("DELETE FROM program WHERE Program_ID=?");
        $stmt->bind_param("i", $program_id);
        if ($stmt->execute()) {
            $response = ['status' => 'success', 'message' => 'Program deleted successfully!'];
        } else {
            $response = ['status' => 'error', 'message' => 'Error deleting program: ' . $stmt->error];
        }
        $stmt->close();
    }

} elseif ($action === 'fetch_universities') {
    // ✅ Corrected to use `universities` table
    $sql = "SELECT University_ID, University_Name FROM universities ORDER BY University_Name ASC";
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

} else {
    // ✅ Default fetch all programs with university info
    $sql = "SELECT p.Program_ID, p.Program_Name, p.Description, u.University_ID, u.University_Name
            FROM program p
            JOIN universities u ON p.University_ID = u.University_ID
            ORDER BY u.University_Name ASC, p.Program_Name ASC";
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
//******************************** */
