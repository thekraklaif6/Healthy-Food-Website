<?php
$conn = new mysqli('localhost', 'root', '', 'freshplate');
if ($conn->connect_error) { die('DB Error: ' . $conn->connect_error); }

$result = $conn->query("SELECT id, TYPE, NAME, calories, price FROM build_options ORDER BY TYPE, id");
echo "=== BUILD OPTIONS IN DATABASE ===" . PHP_EOL;
while ($row = $result->fetch_assoc()) {
    echo "id=" . $row['id'] . " type=" . $row['TYPE'] . " name=" . $row['NAME'] . " cal=" . $row['calories'] . " price=" . $row['price'] . PHP_EOL;
}

$conn->close();
?>
