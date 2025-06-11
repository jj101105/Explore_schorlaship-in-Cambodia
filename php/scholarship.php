<?php
$host = "localhost";
$user = "root";
$pass = ""; // Your DB password
$dbname = "scholarshipdb"; // Replace with your DB name

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// DELETE
if (isset($_POST['delete']) && isset($_POST['id'])) {
  $id = intval($_POST['id']);
  $stmt = $conn->prepare("DELETE FROM scholarships WHERE id = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  echo json_encode(["status" => "deleted"]);
  exit;
}

// INSERT / UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
  $id = isset($_POST['id']) && $_POST['id'] !== "" ? intval($_POST['id']) : null;
  $name = $_POST['name'];
  $province = $_POST['province'];
  $gpa = $_POST['gpa'];
  $degree = $_POST['degree_level'];
  $type = $_POST['scholarship_type'];
  $fields = $_POST['fields_of_study'];
  $college_type = $_POST['college_type'];
  $desc = $_POST['description'];

  if ($id) {
    // Update
    $stmt = $conn->prepare("UPDATE scholarships SET name=?, province=?, gpa=?, degree_level=?, scholarship_type=?, fields_of_study=?, college_type=?, description=? WHERE id=?");
    $stmt->bind_param("ssdsssssi", $name, $province, $gpa, $degree, $type, $fields, $college_type, $desc, $id);
  } else {
    // Insert
    $stmt = $conn->prepare("INSERT INTO scholarships (name, province, gpa, degree_level, scholarship_type, fields_of_study, college_type, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdsssss", $name, $province, $gpa, $degree, $type, $fields, $college_type, $desc);
  }

  if ($stmt->execute()) {
    echo json_encode(["status" => "success"]);
  } else {
    echo json_encode(["status" => "error", "error" => $conn->error]);
  }
  exit;
}

// FETCH ALL
$result = $conn->query("SELECT * FROM scholarships ORDER BY id DESC");
$data = [];
while ($row = $result->fetch_assoc()) {
  $data[] = $row;
}
echo json_encode($data);
$conn->close();
