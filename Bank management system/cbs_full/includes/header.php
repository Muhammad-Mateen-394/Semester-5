<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if(!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Core Banking System</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>

<body>
  
  <?php include __DIR__ . "/navbar.php"; ?>

<div class="container-fluid">
  <div class="row">
<?php include "sidebar.php"; ?>
<div class="main-content col-md-10 ms-sm-auto p-4">
