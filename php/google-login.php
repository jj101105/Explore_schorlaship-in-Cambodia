<?php
require_once 'vendor/autoload.php'; // Adjust path if needed
session_start();

// Google Client Setup
$client = new Google_Client();
$client->setClientId('YOUR_GOOGLE_CLIENT_ID');
$client->setClientSecret('YOUR_GOOGLE_CLIENT_SECRET');
$client->setRedirectUri('http://localhost/yourproject/php/google-login.php'); // update path
$client->addScope('email');
$client->addScope('profile');

if (!isset($_GET['code'])) {
    // Redirect to Google's OAuth 2.0 server
    $authUrl = $client->createAuthUrl();
    header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
    exit();
} else {
    $client->authenticate($_GET['code']);
    $token = $client->getAccessToken();
    $client->setAccessToken($token);

    $oauth = new Google_Service_Oauth2($client);
    $userData = $oauth->userinfo->get();

    $email = $userData->email;
    $name = $userData->name;

    // Connect to your DB
    $conn = new mysqli("localhost", "root", "", "userdb");

    if ($conn->connect_error) {
        die("DB Error");
    }

    // Check if user exists
    $check = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows === 0) {
        // Insert new user
        $insert = $conn->prepare("INSERT INTO users (firstname, email, password) VALUES (?, ?, '')");
        $insert->bind_param("ss", $name, $email);
        $insert->execute();
    }

    echo "<script>
        alert('✅ Google Login Successful!');
        window.location.href = '../html/explore-now.html';
    </script>";
}
?>
