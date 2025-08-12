<?php
header('Content-Type: application/json');
require_once 'connection.php';

// If "University_Slug" is provided → get that university's details
if (isset($_GET['University_Slug']) && !empty($_GET['University_Slug'])) {
    $slug = $_GET['University_Slug'];

    $stmt = $conn->prepare("SELECT * FROM universities WHERE University_Slug = ?");
    $stmt->bind_param("s", $slug);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode([
            "status" => "success",
            "data" => $result->fetch_assoc()
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "University not found"
        ]);
    }
}
// If no "University_Slug" → get all universities
else {
    $result = $conn->query("SELECT University_Name, University_Slug FROM universities ORDER BY University_Name ASC");

    if ($result->num_rows > 0) {
        $universities = [];
        while ($row = $result->fetch_assoc()) {
            $universities[] = $row;
        }
        echo json_encode([
            "status" => "success",
            "data" => $universities
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "No universities found"
        ]);
    }
}

$conn->close();
?>
