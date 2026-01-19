<?php
// auth/install_admin.php
require_once __DIR__ . "/../config/db.php";

$username = 'admin';
$password = 'admin123'; // change after first login
$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("SELECT id FROM users WHERE username=?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
  $ins = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'admin')");
  $ins->bind_param("ss", $username, $hash);
  if ($ins->execute()) {
    echo "Admin installed. Username: admin | Password: admin123<br>";
  } else {
    echo "Failed to create admin: " . $ins->error;
  }
  $ins->close();
} else {
  echo "Admin user already exists. Delete record first if you want to recreate.";
}
$stmt->close();
$conn->close();
?>