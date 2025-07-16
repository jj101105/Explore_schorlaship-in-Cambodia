<?php
session_start();
$appId = 'YOUR_APP_ID';
$appSecret = 'YOUR_APP_SECRET';
$redirectURL = 'http://localhost/yourproject/php/facebook-login.php';

if (!isset($_GET['code'])) {
    $fbLoginUrl = "https://www.facebook.com/v17.0/dialog/oauth?client_id={$appId}&redirect_uri={$redirectURL}&scope=email";
    header("Location: $fbLoginUrl");
    exit;
} else {
    $code = $_GET['code'];
    $tokenURL = "https://graph.facebook.com/v17.0/oauth/access_token?client_id={$appId}&redirect_uri={$redirectURL}&client_secret={$appSecret}&code={$code}";

    $response = file_get_contents($tokenURL);
    $data = json_decode($response, true);
    $accessToken = $data['access_token'];

    // Fetch user info
    $userDataURL = "https://graph.facebook.com/me?fields=name,email&access_token={$accessToken}";
    $userData = json_decode(file_get_contents($userDataURL), true);

    $email = $userData['email'];
    $name = $userData['name'];

    // DB Logic
    $conn = new mysqli("localhost", "root", "", "userdb");
    if ($conn->connect_error) {
        die("DB Error");
    }

    $check = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows === 0) {
        $insert = $conn->prepare("INSERT INTO users (firstname, email, password) VALUES (?, ?, '')");
        $insert->bind_param("ss", $name, $email);
        $insert->execute();
    }

    echo "<script>
        alert('✅ Facebook Login Successful!');
        window.location.href = '../html/explore-now.html';
    </script>";
}
?>
