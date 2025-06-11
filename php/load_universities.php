<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "scholarshipdb";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM university ORDER BY University_ID DESC";
$result = $conn->query($sql);
$i = 1;

while ($row = $result->fetch_assoc()) {
    echo "<tr class='hover:bg-gray-50'>";
    echo "<td class='p-3 border'>" . $i++ . "</td>";
    echo "<td class='p-3 border'>" . htmlspecialchars($row['University_Name']) . "</td>";
    echo "<td class='p-3 border'>" . htmlspecialchars($row['Location']) . "</td>";
    echo "<td class='p-3 border'>" . htmlspecialchars($row['Type']) . "</td>";
    echo "<td class='p-3 border'><a href='" . htmlspecialchars($row['Website']) . "' class='text-blue-600 hover:underline'>" . htmlspecialchars($row['Website']) . "</a></td>";
    echo "<td class='p-3 border'>
            <button class='text-blue-600 hover:underline mr-2'>Edit</button>
            <button class='text-red-600 hover:underline'>Delete</button>
          </td>";
    echo "</tr>";
}
?>