<?php
if(!isset($_SESSION)) session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CBS - Customer Panel</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Customer Layout Styles -->
    <style>
        body { background: #f4f7fc; }

        #customer-sidebar {
            width: 240px;
            height: 100vh;
            position: fixed;
            background: #ffffff;
            border-right: 1px solid #dcdcdc;
            padding-top: 15px;
        }

        .content-area {
            margin-left: 260px;
            margin-top: 20px;
            padding: 20px;
        }
    </style>
</head>
<body>

<?php include __DIR__ . "/nav.php"; ?>
