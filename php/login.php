<?php
session_start();

// Database connection
$host = "localhost";
$user = "root";
$password = "";
$dbname = "userdb";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// Get input and trim spaces
$loginInput = trim($_POST['email']); // email OR phone
$passwordInput = trim($_POST['password']);

// Prepare query to check by email or phone
$sql = "SELECT * FROM users WHERE TRIM(email) = ? OR TRIM(contact_number) = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $loginInput, $loginInput);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $dbPassword = trim($row['password']); // plain text

    if ($passwordInput === $dbPassword) {
        // Store user info in session
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['first_name'] = $row['firstname'];
        $_SESSION['last_name'] = $row['lastname'] ?? '';
        $_SESSION['full_name'] = trim($row['firstname'] . ' ' . ($row['lastname'] ?? ''));
        $_SESSION['email'] = $row['email'];
        $_SESSION['contact'] = $row['contact_number'] ?? '';
        $_SESSION['initials'] = strtoupper(substr($row['firstname'], 0, 1) . substr($row['lastname'] ?? '', 0, 1));

        // Redirect after successful login
        header("Location: ../index.html?justLoggedIn=1");
        exit();
    } else {
        echo "<script>
                alert('❌ Incorrect password.');
                window.history.back();
              </script>";
    }
} else {
    echo "<script>
            alert('❌ Email or phone number not found.');
            window.history.back();
          </script>";
}

$stmt->close();
$conn->close();
?>
