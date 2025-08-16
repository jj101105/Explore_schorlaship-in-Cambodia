<?php
session_start();

// --- Database connection ---
$host = "localhost";
$user = "root";
$pass = "";
$db   = "userdb";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Step 1: Verify Email
if (isset($_POST['verify_email'])) {
    $email = $_POST['email'];

    $stmt = $conn->prepare("SELECT id, firstname, last_name FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['reset_email'] = $email; // store email for next step
        $showResetForm = true;
        $success = "Hello " . $user['firstname'] . " " . $user['last_name'] . ", please enter your new password below.";
    } else {
        $error = "Email not found!";
    }
}

// Step 2: Reset Password
if (isset($_POST['reset_password'])) {
    if (!empty($_SESSION['reset_email'])) {
        $email = $_SESSION['reset_email'];
        $newPassword = $_POST['password'];

        $stmt = $conn->prepare("UPDATE users SET password=? WHERE email=?");
        $stmt->bind_param("ss", $newPassword, $email);

        if ($stmt->execute()) {
            // Automatically log in user
            $stmt = $conn->prepare("SELECT id, firstname FROM users WHERE email=?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['firstname'];

            // Clear reset_email session
            unset($_SESSION['reset_email']);

            // Redirect to index.html
            header("Location: ../index.html");
            exit;
        } else {
            $error = "Failed to update password!";
        }
    } else {
        $error = "Session expired. Please verify your email again.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 400px; margin: auto; background: #fff; padding: 20px;
                     border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        input[type=email], input[type=password] { width: 100%; padding: 10px; margin: 10px 0;
                                                  border: 1px solid #ccc; border-radius: 5px; }
        button { width: 100%; padding: 10px; background: #007bff; color: #fff;
                 border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #0056b3; }
        .msg { margin: 10px 0; color: green; }
        .error { margin: 10px 0; color: red; }
    </style>
</head>
<body>
<div class="container">
    <h2>Reset Password</h2>

    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    <?php if (isset($success)) echo "<p class='msg'>$success</p>"; ?>

    <?php if (!isset($showResetForm)) { ?>
        <!-- Step 1: Verify Email -->
        <form method="POST">
            <input type="email" name="email" placeholder="Enter your registered email" required>
            <button type="submit" name="verify_email">Verify Email</button>
        </form>
    <?php } else { ?>
        <!-- Step 2: Reset Password -->
        <form method="POST">
            <input type="password" name="password" placeholder="Enter new password" required>
            <button type="submit" name="reset_password">Reset Password</button>
        </form>
    <?php } ?>
</div>
</body>
</html>
