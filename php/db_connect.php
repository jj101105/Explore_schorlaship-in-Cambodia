<?php
// Database credentials
$host = "localhost";
$user = "root";
$password = "";

// Connect to userdb
$userdb = new mysqli($host, $user, $password, "userdb");
if ($userdb->connect_error) {
    die("❌ UserDB Connection failed: " . $userdb->connect_error);
}

// Connect to scholarshipdb
$scholardb = new mysqli($host, $user, $password, "scholarshipdb");
if ($scholardb->connect_error) {
    die("❌ ScholarshipDB Connection failed: " . $scholardb->connect_error);
}
?>
