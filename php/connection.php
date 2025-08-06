<?php
$host = "localhost";
$dbname = "scholarshipdb";
$user = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected successfully"; // For debugging, remove in production
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
