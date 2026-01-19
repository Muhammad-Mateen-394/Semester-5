<?php
require_once __DIR__ . "/../config/db.php";
session_start();
if(!isset($_SESSION['user'])) header("Location: ../auth/login.php");
include __DIR__ . "/../includes/header.php";
?>
<?php
if(isset($_SESSION['customer_credentials'])) {
    $creds = $_SESSION['customer_credentials'];
    ?>
    <div class="alert alert-success">
        <strong>Customer Login Credentials:</strong><br>
        Username: <b><?= $creds['username'] ?></b><br>
        Password: <b><?= $creds['password'] ?></b>
    </div>
    <?php

    // delete after showing
    unset($_SESSION['customer_credentials']);
}
?>
<div class="col-md-10 p-4">
  <div class="d-flex justify-content-between align-items-center">
    <h4>Customers</h4>
    <a href="add.php" class="btn btn-primary">Add Customer</a>
  </div>

  <table class="table mt-3">
    <thead class="table-light">
      <tr><th>#</th><th>Name</th><th>CNIC</th><th>Contact</th><th>Email</th><th>Actions</th></tr>
    </thead>
    <tbody>
      <?php
      $res = $conn->query("SELECT * FROM customers ORDER BY customer_id");
      while($row = $res->fetch_assoc()) {
          echo "<tr>";
          echo "<td>".htmlspecialchars($row['customer_id'])."</td>";
          echo "<td>".htmlspecialchars($row['name'])."</td>";
          echo "<td>".htmlspecialchars($row['cnic'])."</td>";
          echo "<td>".htmlspecialchars($row['contact'])."</td>";
          echo "<td>".htmlspecialchars($row['email'])."</td>";
          echo "<td>
                  <a class='btn btn-sm btn-outline-secondary' href='edit.php?id=".$row['customer_id']."'>Edit</a>
                  <a class='btn btn-sm btn-danger' href='delete.php?id=".$row['customer_id']."' onclick='return confirm(\"Delete?\")'>Delete</a>
                </td>";
          echo "</tr>";
      }
      ?>
    </tbody>
  </table>
</div>
<?php include __DIR__ . "/../includes/footer.php"; ?>