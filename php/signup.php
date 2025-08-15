<?php
session_start(); // Start session at the top

$host = "localhost";
$user = "root";
$password = ""; // default for XAMPP
$dbname = "userdb";

// Connect to database
$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Sanitize inputs
$firstname = trim($_POST['firstname'] ?? '');
$last_name = trim($_POST['lastname'] ?? ''); // match table column
$email = trim($_POST['email'] ?? '');
$pwd = $_POST['password'] ?? '';
$repeat_pwd = $_POST['repeat-password'] ?? '';

// Basic validation
if (empty($firstname) || empty($last_name) || empty($email) || empty($pwd) || empty($repeat_pwd)) {
    die("Please fill in all required fields.");
}

if ($pwd !== $repeat_pwd) {
    die("Passwords do not match.");
}

// Check if email already exists
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    die("This email is already registered.");
}
$stmt->close();

$created_at = date("Y-m-d H:i:s"); // use correct column name

// Insert user into database (password stored as plain text)
$stmt = $conn->prepare("INSERT INTO users (firstname, last_name, email, password, created_at) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $firstname, $last_name, $email, $pwd, $created_at);

if ($stmt->execute()) {
    $_SESSION['user_id'] = $conn->insert_id;
    $_SESSION['username'] = $firstname;

    header("Location: ../index.html?justLoggedIn=1");
    exit();
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
