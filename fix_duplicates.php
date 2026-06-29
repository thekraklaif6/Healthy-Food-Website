<?php
$conn = new mysqli('localhost', 'root', '', 'freshplate');
if ($conn->connect_error) { die('DB Error: ' . $conn->connect_error); }

// Delete duplicate build_options (keep ids 1-12, remove 13-24)
$conn->query("DELETE FROM build_options WHERE id > 12");

echo "Duplicate build_options removed." . PHP_EOL;

// Verify
$result = $conn->query("SELECT id, TYPE, NAME FROM build_options ORDER BY TYPE, id");
echo PHP_EOL . "=== REMAINING BUILD OPTIONS ===" . PHP_EOL;
while ($row = $result->fetch_assoc()) {
    echo "id=" . $row['id'] . " type=" . $row['TYPE'] . " name=" . $row['NAME'] . PHP_EOL;
}

$conn->close();
?>
