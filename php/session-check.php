<?php
session_start();
header('Content-Type: application/json');
if (isset($_SESSION['user_fullname'])) {
    echo json_encode([
        "loggedIn" => true,
        "fullname" => $_SESSION['user_fullname']
    ]);
} else {
    echo json_encode([
        "loggedIn" => false
    ]);
}
?>
