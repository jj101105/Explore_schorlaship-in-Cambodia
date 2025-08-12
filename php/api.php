<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Allow CORS
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle CORS preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";  // Set your DB password here
$dbname = "scholarshipdb";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'DB connection failed: ' . $conn->connect_error]);
    exit;
}

// Get action from GET or POST params
$action = $_GET['action'] ?? ($_POST['action'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'get') {
    // Fetch all scholarships
    $sql = "SELECT * FROM scholarships";
    $result = $conn->query($sql);
    $data = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Decode JSON columns
            $row['details'] = json_decode($row['details'], true) ?: [];
            $row['benefits'] = json_decode($row['benefits'], true) ?: [];
            $row['eligibility'] = json_decode($row['eligibility'], true) ?: [];
            $row['eligibleCountries'] = json_decode($row['eligibleCountries'], true) ?: [];
            $row['requiredDocuments'] = json_decode($row['requiredDocuments'], true) ?: [];
            $data[] = $row;
        }
    }
    echo json_encode($data);
    exit;
} 

elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read input either JSON (application/json) or form-data
    $contentType = $_SERVER["CONTENT_TYPE"] ?? '';
    $input = [];

    if (strpos($contentType, 'application/json') !== false) {
        $input = json_decode(file_get_contents('php://input'), true);
    } else {
        // fallback for form-data (for delete)
        $input = $_POST;
    }

    if (!$action) {
        echo json_encode(['success' => false, 'message' => 'No action specified']);
        exit;
    }

    if ($action === 'add') {
        // Prepare JSON encoded fields
        $details = json_encode($input['details'] ?? []);
        $benefits = json_encode($input['benefits'] ?? []);
        $eligibility = json_encode($input['eligibility'] ?? []);
        $eligibleCountries = json_encode($input['eligibleCountries'] ?? []);
        $requiredDocuments = json_encode($input['requiredDocuments'] ?? []);

        $sql = "INSERT INTO scholarships 
            (title, subtitle, logo, details, description, benefits, university, major, eligibility, eligibleCountries, requiredDocuments, howToApply) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
            exit;
        }
        $stmt->bind_param(
            "ssssssssssss",
            $input['title'],
            $input['subtitle'],
            $input['logo'],
            $details,
            $input['description'],
            $benefits,
            $input['university'],
            $input['major'],
            $eligibility,
            $eligibleCountries,
            $requiredDocuments,
            $input['howToApply']
        );
        if (!$stmt->execute()) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Execute failed: ' . $stmt->error]);
            exit;
        }
        echo json_encode(['success' => true, 'message' => 'Scholarship added', 'id' => $conn->insert_id]);
        exit;
    }

    elseif ($action === 'update') {
        if (empty($input['id']) || !is_numeric($input['id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid or missing scholarship ID']);
            exit;
        }
        $id = (int)$input['id'];

        $details = json_encode($input['details'] ?? []);
        $benefits = json_encode($input['benefits'] ?? []);
        $eligibility = json_encode($input['eligibility'] ?? []);
        $eligibleCountries = json_encode($input['eligibleCountries'] ?? []);
        $requiredDocuments = json_encode($input['requiredDocuments'] ?? []);

        $sql = "UPDATE scholarships SET 
            title=?, subtitle=?, logo=?, details=?, description=?, benefits=?, university=?, major=?, eligibility=?, eligibleCountries=?, requiredDocuments=?, howToApply=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
            exit;
        }

        $stmt->bind_param(
            "ssssssssssssi",
            $input['title'],
            $input['subtitle'],
            $input['logo'],
            $details,
            $input['description'],
            $benefits,
            $input['university'],
            $input['major'],
            $eligibility,
            $eligibleCountries,
            $requiredDocuments,
            $input['howToApply'],
            $id
        );

        if (!$stmt->execute()) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Execute failed: ' . $stmt->error]);
            exit;
        }
        echo json_encode(['success' => true, 'message' => 'Scholarship updated']);
        exit;
    }

    elseif ($action === 'delete') {
        $id = isset($input['id']) && is_numeric($input['id']) ? (int)$input['id'] : 0;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid scholarship ID']);
            exit;
        }

        $sql = "DELETE FROM scholarships WHERE id=?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
            exit;
        }
        $stmt->bind_param("i", $id);
        if (!$stmt->execute()) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Execute failed: ' . $stmt->error]);
            exit;
        }
        echo json_encode(['success' => true, 'message' => 'Scholarship deleted']);
        exit;
    }

    else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Unknown action']);
        exit;
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}

$conn->close();
