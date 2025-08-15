<?php
// filepath: c:\xampp\htdocs\Explore_schorlaship-in-Cambodia\php\get_scholarships.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// --- Start session to get logged-in user info ---
session_start();
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// --- Connect to scholarshipdb ---
$scholardb = new mysqli('localhost', 'root', '', 'scholarshipdb');
if ($scholardb->connect_error) {
    echo json_encode(['error' => 'ScholarshipDB connection failed']);
    http_response_code(500);
    exit;
}

// --- Connect to userdb ---
$userdb = new mysqli('localhost', 'root', '', 'userdb');
if ($userdb->connect_error) {
    echo json_encode(['error' => 'UserDB connection failed']);
    http_response_code(500);
    exit;
}

// --- Fetch scholarships ---
$result = $scholardb->query("SELECT * FROM scholarships ORDER BY id DESC");
$scholarships = [];

while ($row = $result->fetch_assoc()) {

    // Get total favorites for this scholarship
    $stmt = $userdb->prepare("SELECT COUNT(*) AS count FROM user_favorites WHERE scholarship_id=?");
    $stmt->bind_param("i", $row['id']);
    $stmt->execute();
    $res = $stmt->get_result();
    $favRow = $res->fetch_assoc();
    $totalFavorites = intval($favRow['count']);
    $stmt->close();

    // Check if CURRENT USER favorited this scholarship
    $isFavoritedByUser = false;
    if ($userId) {
        $stmt2 = $userdb->prepare("SELECT COUNT(*) AS count FROM user_favorites WHERE user_id=? AND scholarship_id=?");
        $stmt2->bind_param("ii", $userId, $row['id']);
        $stmt2->execute();
        $res2 = $stmt2->get_result();
        $row2 = $res2->fetch_assoc();
        $isFavoritedByUser = $row2['count'] > 0;
        $stmt2->close();
    }

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
        "favorites" => $totalFavorites,               // total count
        "isFavoritedByUser" => $isFavoritedByUser,   // dark heart only if current user favorited
        "details" => isset($row["details"]) && $row["details"] ? json_decode($row["details"], true) : [],
        "eligibility" => isset($row["eligibility"]) && $row["eligibility"] ? json_decode($row["eligibility"], true) : []
    ];
}

echo json_encode($scholarships);

// --- Close connections ---
$scholardb->close();
$userdb->close();
?>
