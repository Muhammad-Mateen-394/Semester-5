<?php
require_once "../config/db.php";
session_start();
if(!isset($_SESSION['user'])) header("Location: ../auth/login.php");

$id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM customers WHERE customer_id=?");
$stmt->bind_param("i", $id);

if($stmt->execute()){
    audit_log($conn, "Delete Customer", "customers", $_SESSION['user'], "Customer ID: $id");
}

header("Location: view.php");
exit;
