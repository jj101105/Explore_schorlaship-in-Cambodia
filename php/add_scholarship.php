<?php
// Simple check (You should add authentication for admin in real app)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
    exit();
}

$mysqli = new mysqli("localhost", "root", "", "scholarshipdb");

if ($mysqli->connect_errno) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed"]);
    exit();
}

// Receive POST data (expected as JSON)
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid JSON input"]);
    exit();
}

// Prepare JSON string fields (arrays)
$tags = json_encode($data['tags']);
$benefits = json_encode($data['benefits']);
$eligibility_detailed_requirements = json_encode($data['eligibility']['detailedRequirements']);
$eligible_countries = json_encode($data['eligibleCountries']);
$required_documents = json_encode($data['requiredDocuments']);

$stmt = $mysqli->prepare("INSERT INTO scholarships1 (title, subtitle, logo, favorites, tags, study_in, type, degree, deadline, description, benefits, university, major, eligibility_gpa, eligibility_max_age, eligibility_grade, eligibility_gender, eligibility_ielts, eligibility_work_experience, eligibility_detailed_requirements, eligible_countries, required_documents, how_to_apply) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param("sssisssssssssssssssssss",
    $data['title'],
    $data['subtitle'],
    $data['logo'],
    $data['favorites'],
    $tags,
    $data['details']['studyIn'],
    $data['details']['type'],
    $data['details']['degree'],
    $data['details']['deadline'],
    $data['description'],
    $benefits,
    $data['university'],
    $data['major'],
    $data['eligibility']['gpa'],
    $data['eligibility']['maxAge'],
    $data['eligibility']['grade'],
    $data['eligibility']['gender'],
    $data['eligibility']['ielts'],
    $data['eligibility']['workExperience'],
    $eligibility_detailed_requirements,
    $eligible_countries,
    $required_documents,
    $data['howToApply']
);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Scholarship added"]);
} else {
    http_response_code(500);
    echo json_encode(["error" => $stmt->error]);
}

$stmt->close();
$mysqli->close();
?>
