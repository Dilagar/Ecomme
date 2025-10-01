<?php
require_once __DIR__ . '/../config/db.php';

// Add password_hash column to admins table
$sql = "ALTER TABLE admins ADD COLUMN password_hash VARCHAR(255) DEFAULT NULL AFTER password";
$result = mysqli_query($conn, $sql);

if ($result) {
    echo "Database updated successfully. The password_hash column has been added to the admins table.";
} else {
    echo "Error updating database: " . mysqli_error($conn);
}
?>