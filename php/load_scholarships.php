<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "scholarshipdb";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM scholarships ORDER BY id DESC";
$result = $conn->query($sql);
$i = 1;

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $id = $row['id'];
        echo "<tr 
                data-id='{$id}' 
                data-description=\"" . htmlspecialchars($row['description'], ENT_QUOTES) . "\" 
                class='hover:bg-gray-50'>
              ";
        echo "<td class='p-3 border'>" . $i++ . "</td>";
        echo "<td class='p-3 border scholarship-name'>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td class='p-3 border scholarship-province'>" . htmlspecialchars($row['province']) . "</td>";
        echo "<td class='p-3 border scholarship-gpa'>" . htmlspecialchars($row['gpa']) . "</td>";
        echo "<td class='p-3 border scholarship-degree'>" . htmlspecialchars($row['degree_level']) . "</td>";
        echo "<td class='p-3 border scholarship-type'>" . htmlspecialchars($row['type']) . "</td>";
        echo "<td class='p-3 border scholarship-fields'>" . htmlspecialchars($row['fields_of_study']) . "</td>";
        echo "<td class='p-3 border scholarship-college'>" . htmlspecialchars($row['college_type']) . "</td>";
        echo "<td class='p-3 border'>
                <button class='text-blue-600 hover:underline mr-2 edit-btn'>Edit</button>
                <button class='text-red-600 hover:underline delete-btn'>Delete</button>
              </td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='9' class='p-3 border text-center'>No scholarships found</td></tr>";
}

$conn->close();
?>
