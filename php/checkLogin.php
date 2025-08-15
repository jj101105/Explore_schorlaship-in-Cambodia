<?php
session_start();
header('Content-Type: application/json');

if (isset($_SESSION['user_id'])) {
    echo json_encode([
        "loggedIn" => true,
        "user" => [
            "id" => $_SESSION['user_id'],
            "name" => $_SESSION['full_name'],
            "email" => $_SESSION['email']
        ]
    ]);
} else {
    echo json_encode(["loggedIn" => false]);
}
?>
