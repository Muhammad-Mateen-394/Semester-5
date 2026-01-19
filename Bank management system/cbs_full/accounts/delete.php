<?php
require_once __DIR__ . "/../config/db.php";
session_start(); if(!isset($_SESSION['user'])) header("Location: ../auth/login.php");
$id = intval($_GET['id'] ?? 0);
if($id > 0){
    $stmt = $conn->prepare("DELETE FROM accounts WHERE account_no = ?");
    $stmt->bind_param("i", $id);
    if($stmt->execute()){
        audit_log($conn, 'Delete Account', 'accounts', $_SESSION['user'], "AccountNo:$id");
    }
}
header("Location: view.php");
exit;