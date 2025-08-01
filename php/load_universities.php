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
    echo "<tr class='hover:bg-gray-50' data-id='{$row['University_ID']}'>";
echo "<td class='p-3 border'>" . $i++ . "</td>";
echo "<td class='p-3 border university-name'>" . htmlspecialchars($row['University_Name']) . "</td>";
echo "<td class='p-3 border university-location'>" . htmlspecialchars($row['Location']) . "</td>";
echo "<td class='p-3 border university-type'>" . htmlspecialchars($row['Type']) . "</td>";
echo "<td class='p-3 border university-website'><a href='" . htmlspecialchars($row['Website']) . "' class='text-blue-600 hover:underline' target='_blank'>" . htmlspecialchars($row['Website']) . "</a></td>";
echo "<td class='p-3 border university-description'>" . htmlspecialchars($row['Description']) . "</td>";

echo "<td class='p-3 border actions-cell'>
        <button class='text-blue-600 hover:underline mr-2 edit-btn'>Edit</button>
        <button class='text-red-600 hover:underline delete-btn'>Delete</button>
      </td>";
echo "</tr>";

    $i++;
}
?>