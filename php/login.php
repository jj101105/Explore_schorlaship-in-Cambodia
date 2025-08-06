<?php
// Database connection details
$host = "localhost";
$user = "root";
$password = "";
$dbname = "userdb";

// Create database connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// Get input from form
$email = $_POST['email'];
$passwordInput = $_POST['password'];

// Check if user exists
$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

// If user found
if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $hashedPassword = $row['password'];

    // Verify password
   //----------------------------------------
if (password_verify($passwordInput, $hashedPassword)) {
    session_start();
$_SESSION['username'] = $row['firstname'];

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
            alert('❌ Email not found.');
            window.history.back();
          </script>";
}



// Close connections
$stmt->close();
$conn->close();
?>
