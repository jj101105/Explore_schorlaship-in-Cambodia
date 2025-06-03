<?php
$host = "localhost";
$user = "root";
$password = ""; // default for XAMPP
$dbname = "userdb";

// Debug: Check form data
var_dump($_POST);

// Connect to database
$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "✅ Connected to DB<br>";
}

// Sanitize and validate input
$firstname = trim($_POST['firstname']);
$email = trim($_POST['email']);
$password = $_POST['password'];
$created_at = date("Y-m-d H:i:s");

// Hash password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Debug: Print inputs
echo "Firstname: $firstname<br>";
echo "Email: $email<br>";
echo "Password (hashed): $hashedPassword<br>";
echo "Created At: $created_at<br>";

// Use prepared statement to prevent SQL injection
$stmt = $conn->prepare("INSERT INTO users (firstname, email, password, created_at) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $firstname, $email, $hashedPassword, $created_at);

if ($stmt->execute()) {
    echo "✅ User registered successfully!";
    // header("Location: success.html");
    // exit();
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>