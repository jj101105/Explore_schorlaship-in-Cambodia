<?php
$host = "localhost";
$username = "root";
$password = ""; // default XAMPP password
$dbname = "scholarshipdb";

// 1. Connect to database
$conn = new mysqli($host, $username, $password, $dbname);

// 2. Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 3. Get form data
$name = $_POST['name'];
$email = $_POST['email'];
$message = $_POST['message'];

// 4. Insert into database
$sql = "INSERT INTO contacts (name, email, message) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $name, $email, $message);

if ($stmt->execute()) {
    echo "<script>alert('Message sent successfully!'); window.location.href='index.html';</script>";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
