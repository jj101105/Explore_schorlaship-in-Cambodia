<?php
// filepath: c:\xampp\htdocs\Explore_schorlaship-in-Cambodia\php\get_scholarships.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
$conn = new mysqli('localhost', 'root', '', 'scholarshipdb');
if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed']);
    http_response_code(500);
    exit;
}
$result = $conn->query("SELECT * FROM scholarships ORDER BY id DESC");
$scholarships = [];
while ($row = $result->fetch_assoc()) {
    $scholarships[] = [
        "id" => $row["id"],
        "logo" => $row["logo"],
        "title" => $row["title"],
        "subtitle" => $row["subtitle"],
        "description" => $row["description"],
        "university" => $row["university"],
        "major" => $row["major"],
        "benefits" => isset($row["benefits"]) && $row["benefits"] ? json_decode($row["benefits"], true) : [],
        "howToApply" => isset($row["howToApply"]) ? $row["howToApply"] : "",
        "eligibleCountries" => isset($row["eligibleCountries"]) && $row["eligibleCountries"] ? json_decode($row["eligibleCountries"], true) : [],
        "requiredDocuments" => isset($row["requiredDocuments"]) && $row["requiredDocuments"] ? json_decode($row["requiredDocuments"], true) : [],
        "tags" => isset($row["tags"]) && $row["tags"] ? json_decode($row["tags"], true) : [],
        "favorites" => isset($row["favorites"]) ? intval($row["favorites"]) : 0,
        "details" => isset($row["details"]) && $row["details"] ? json_decode($row["details"], true) : [],
        "eligibility" => isset($row["eligibility"]) && $row["eligibility"] ? json_decode($row["eligibility"], true) : []
    ];
}
echo json_encode($scholarships);
$conn->close();