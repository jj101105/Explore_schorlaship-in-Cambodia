<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "scholarshipdb";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

date_default_timezone_set("Asia/Phnom_Penh");
$created_at = date("Y-m-d H:i:s");
$action = $_POST['action'] ?? '';

$name = $_POST['scholarship_name'] ?? '';
$province = $_POST['province'] ?? '';
$gpa = isset($_POST['gpa']) ? floatval($_POST['gpa']) : 0;
$degree = $_POST['degree_level'] ?? '';
$type = $_POST['scholarship_type'] ?? '';
$fields = $_POST['fields_of_study'] ?? '';
$collegeType = $_POST['college_type'] ?? '';
$deadline = $_POST['deadline'] ?? null;
$sponsor = $_POST['sponsor'] ?? '';
$benefits = $_POST['benefits'] ?? '';
$apply = $_POST['apply'] ?? '';
$link = $_POST['link'] ?? '';
$description = $_POST['description'] ?? '';

if ($action === 'add') {
  $sql = "INSERT INTO scholarships1 (name, province, gpa, degree_level, type, fields_of_study, college_type, deadline, sponsor, benefits, apply, link, description, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
  $stmt = $conn->prepare($sql);
  if (!$stmt) {
    echo "Prepare failed: " . $conn->error;
    exit;
  }
  $stmt->bind_param("ssdsssssssssss", $name, $province, $gpa, $degree, $type, $fields, $collegeType, $deadline, $sponsor, $benefits, $apply, $link, $description, $created_at);
  $res = $stmt->execute();
  echo $res ? "added" : "Insert failed: " . $stmt->error;
  $stmt->close();

} elseif ($action === 'edit') {
  $id = intval($_POST['id']);
  $sql = "UPDATE scholarships1 SET name=?, province=?, gpa=?, degree_level=?, type=?, fields_of_study=?, college_type=?, deadline=?, sponsor=?, benefits=?, apply=?, link=?, description=? WHERE id=?";
  $stmt = $conn->prepare($sql);
  if (!$stmt) {
    echo "Prepare failed: " . $conn->error;
    exit;
  }
  // 13 params + id = 14 params
  $stmt->bind_param("ssdssssssssssi", $name, $province, $gpa, $degree, $type, $fields, $collegeType, $deadline, $sponsor, $benefits, $apply, $link, $description, $id);
  $res = $stmt->execute();
  echo $res ? "edited" : "Update failed: " . $stmt->error;
  $stmt->close();

} elseif ($action === 'delete') {
  $id = intval($_POST['id']);
  $sql = "DELETE FROM scholarships1 WHERE id=?";
  $stmt = $conn->prepare($sql);
  if (!$stmt) {
    echo "Prepare failed: " . $conn->error;
    exit;
  }
  $stmt->bind_param("i", $id);
  $res = $stmt->execute();
  echo $res ? "deleted" : "Delete failed: " . $stmt->error;
  $stmt->close();

} else {
  echo "Invalid action";
}

$conn->close();
?>
