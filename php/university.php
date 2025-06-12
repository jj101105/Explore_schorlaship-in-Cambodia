<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "scholarshipdb";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$action = $_POST['action'] ?? '';

if ($action === 'add') {
    $name = $_POST['name'];
    $type = $_POST['type'];
    $location = $_POST['location'];
    $website = $_POST['website'] ?? '';
    $description = $_POST['description'] ?? '';

    $sql = "INSERT INTO university (University_Name, Type, Location, Website, Description) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $name, $type, $location, $website, $description);
    $stmt->execute();

    echo "added";
}
elseif ($action === 'edit') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $location = $_POST['location'];
    $type = $_POST['type'];
    $website = $_POST['website'];

    $sql = "UPDATE university SET University_Name=?, Location=?, Type=?, Website=? WHERE University_ID=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $name, $location, $type, $website, $id);
    
    if ($stmt->execute()) {
        echo "edited";
    } else {
        echo "error: " . $stmt->error;
    }
}
elseif ($action === 'delete') {
    $id = $_POST['id'];
    $sql = "DELETE FROM university WHERE University_ID=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "deleted";
    } else {
        echo "error: " . $stmt->error;
    }
}
else {
    echo "invalid action";
}
?>