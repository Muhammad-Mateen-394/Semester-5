<?php
// config/db.php
declare(strict_types=1);

$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = 'Mateen_2005';   // change if needed
$DB_NAME = 'cbs';

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}
$conn->set_charset('utf8mb4');

function audit_log(mysqli $conn, string $operation, string $table, string $user, string $details = ''): void {
    $stmt = $conn->prepare("INSERT INTO auditlog (operation, table_affected, user_name, details) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $operation, $table, $user, $details);
    $stmt->execute();
    $stmt->close();
}
?>