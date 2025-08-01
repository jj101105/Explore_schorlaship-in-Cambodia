<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "scholarshipdb";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Fetch scholarships ordered by created date desc
$sql = "SELECT * FROM scholarships1 ORDER BY created_at DESC";
$result = $conn->query($sql);

if (!$result) {
  echo "<tr><td colspan='9'>Error loading scholarships: " . $conn->error . "</td></tr>";
  exit;
}

if ($result->num_rows === 0) {
  echo "<tr><td colspan='9' class='text-center p-3'>No scholarships found.</td></tr>";
  exit;
}

$index = 1;
while ($row = $result->fetch_assoc()) {
  // Escape output to avoid XSS
  $id = htmlspecialchars($row['id']);
  $name = htmlspecialchars($row['name']);
  $province = htmlspecialchars($row['province']);
  $gpa = htmlspecialchars($row['gpa']);
  $degree = htmlspecialchars($row['degree_level']);
  $type = htmlspecialchars($row['type']);
  $fields = htmlspecialchars($row['fields_of_study']);
  $college = htmlspecialchars($row['college_type']);
  $deadline = htmlspecialchars($row['deadline']);
  $sponsor = htmlspecialchars($row['sponsor']);
  $benefits = htmlspecialchars($row['benefits']);
  $apply = htmlspecialchars($row['apply']);
  $link = htmlspecialchars($row['link']);
  $description = htmlspecialchars($row['description']);

  echo "<tr data-id=\"$id\" 
           data-description=\"$description\" 
           data-deadline=\"$deadline\" 
           data-sponsor=\"$sponsor\" 
           data-benefits=\"$benefits\" 
           data-apply=\"$apply\" 
           data-link=\"$link\">
    <td class='p-3 border'>{$index}</td>
    <td class='scholarship-name p-3 border'>{$name}</td>
    <td class='scholarship-province p-3 border'>{$province}</td>
    <td class='scholarship-gpa p-3 border'>{$gpa}</td>
    <td class='scholarship-degree p-3 border'>{$degree}</td>
    <td class='scholarship-type p-3 border'>{$type}</td>
    <td class='scholarship-fields p-3 border'>{$fields}</td>
    <td class='scholarship-college p-3 border'>{$college}</td>
    <td class='p-3 border'>
      <button class='edit-btn text-blue-600 hover:underline mr-2 cursor-pointer'>Edit</button>
      <button class='delete-btn text-red-600 hover:underline cursor-pointer'>Delete</button>
    </td>
  </tr>";
  $index++;
}

$conn->close();
?>
