<?php
require_once 'config/database.php'; // Update path as needed

$db = new Database();
$conn = $db->getConnection();
// print($conn);
$tables = $conn->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
// print($tables); 

echo "Tables found:<br>";
foreach ($tables as $table) {
    echo $table . "<br>";
}
?>
