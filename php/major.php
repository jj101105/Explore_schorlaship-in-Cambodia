<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "scholarshipdb";

// Connect
$conn = new mysqli($host, $user, $pass, $db);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle POST requests for CUD (Create, Update, Delete)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if an 'action' is specified (for edit/delete)
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action == 'edit') {
            if (isset($_POST['id'], $_POST['major_name'], $_POST['field_category'], $_POST['description'])) {
                $id = $_POST['id'];
                $name = $_POST['major_name'];
                $field = $_POST['field_category'];
                $desc = $_POST['description'];

                $stmt = $conn->prepare("UPDATE major SET Major_Name=?, Field_Category=?, Description=? WHERE Major_ID=?");
                $stmt->bind_param("sssi", $name, $field, $desc, $id);

                if ($stmt->execute()) {
                    echo json_encode(["status" => "success"]);
                } else {
                    echo json_encode(["status" => "error", "message" => "Update failed: " . $stmt->error]);
                }
            } else {
                echo json_encode(["status" => "error", "message" => "Missing parameters for edit."]);
            }
        } elseif ($action == 'delete') {
            if (isset($_POST['id'])) {
                $id = $_POST['id'];

                $stmt = $conn->prepare("DELETE FROM major WHERE Major_ID=?");
                $stmt->bind_param("i", $id);

                if ($stmt->execute()) {
                    echo json_encode(["status" => "success"]);
                } else {
                    echo json_encode(["status" => "error", "message" => "Delete failed: " . $stmt->error]);
                }
            } else {
                echo json_encode(["status" => "error", "message" => "Missing ID for delete."]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Unknown action."]);
        }
    }
    // If no specific 'action' is set, assume it's an 'add' operation
    // This condition should only be met if it's explicitly an add, or if 'major_name' is the defining factor for 'add'
    else if (isset($_POST['major_name'], $_POST['field_category'], $_POST['description'])) { // Check all required fields for adding
        $name = $_POST['major_name'];
        $field = $_POST['field_category'];
        $desc = $_POST['description'];

        $stmt = $conn->prepare("INSERT INTO major (Major_Name, Field_Category, Description) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $field, $desc);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Insert failed: " . $stmt->error]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid POST request or missing parameters for add/edit/delete."]);
    }
    exit; // Exit after handling any POST request
}

// Handle GET requests (only for fetching data)
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['fetch'])) {
    $result = $conn->query("SELECT * FROM major ORDER BY Major_ID DESC");
    $data = [];

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        header('Content-Type: application/json');
        echo json_encode($data);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to fetch majors: " . $conn->error]);
    }
    exit; // Exit after handling GET request
}

// If no specific request type is matched
echo json_encode(["status" => "error", "message" => "Invalid request method or parameters."]);

// Close connection (optional, as script termination usually closes it, but good practice)
$conn->close();
?>