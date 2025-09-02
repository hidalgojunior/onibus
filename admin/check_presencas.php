<?php
include '../config/config.php';
$conn = getDatabaseConnection();

echo "Tabela PRESENCAS:\n";
$result = $conn->query('DESCRIBE presencas');
while($row = $result->fetch_assoc()) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}

$conn->close();
?>
