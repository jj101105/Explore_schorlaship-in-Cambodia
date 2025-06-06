<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "scholarshipdb";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
  $id = intval($_GET['id']);
  $stmt = $conn->prepare("DELETE FROM contacts WHERE id = ?");
  $stmt->bind_param("i", $id);

  if ($stmt->execute()) {
    echo "Feedback deleted successfully.";
  } else {
    echo "Failed to delete feedback.";
  }
  $stmt->close();
} else {
  echo "Invalid request.";
}

$conn->close();
?>
