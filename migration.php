<?php
require_once("DB.php");

$query = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE COLUMN_NAME = 'message' AND TABLE_SCHEMA = DATABASE() AND TABLE_NAME NOT IN (SELECT TABLE_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE COLUMN_NAME = 'is_edited' AND TABLE_SCHEMA = DATABASE())";

$result = mysqli_query($conn, $query);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $tableName = $row['TABLE_NAME'];
        $alterQuery = "ALTER TABLE `$tableName` ADD COLUMN is_edited TINYINT(1) DEFAULT 0";
        if (mysqli_query($conn, $alterQuery)) {
            echo "Altered $tableName\n";
        } else {
            echo "Failed to alter $tableName: " . mysqli_error($conn) . "\n";
        }
    }
} else {
    echo "Error querying tables: " . mysqli_error($conn) . "\n";
}
?>
