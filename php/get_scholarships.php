<?php
header('Content-Type: application/json');
// Optional: Enable CORS if needed (adjust origin as necessary)
// header("Access-Control-Allow-Origin: *");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$host = 'localhost';
$dbname = 'scholarshipdb';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT * FROM scholarships1");
    $stmt->execute();
    $scholarships = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($scholarships);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "error" => true,
        "message" => "Database error: " . $e->getMessage()
    ]);
    exit;
}
?>
