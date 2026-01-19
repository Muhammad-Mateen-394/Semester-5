<?php
require_once __DIR__ . "/../config/db.php";
session_start(); if(!isset($_SESSION['user'])) header("Location: ../auth/login.php");
include "../includes/header.php";
?>
<div class="col-md-10 p-4">
  <div class="d-flex justify-content-between">
    <h4>Accounts</h4>
    <a class="btn btn-primary" href="add.php">Open Account</a>
  </div>
  <table class="table mt-3">
    <thead><tr><th>#</th><th>Account No</th><th>Customer</th><th>Type</th><th>Balance</th><th>Action</th></tr></thead>
    <tbody>
      <?php
      $res = $conn->query("SELECT a.account_no, a.type, a.balance, c.name FROM accounts a JOIN customers c ON a.customer_id=c.customer_id ORDER BY a.account_no DESC");
      while($row = $res->fetch_assoc()){
          echo "<tr>";
          echo "<td></td>";
          echo "<td>".$row['account_no']."</td>";
          echo "<td>".htmlspecialchars($row['name'])."</td>";
          echo "<td>".$row['type']."</td>";
          echo "<td>".number_format($row['balance'],2)."</td>";
          echo "<td>
                  <a class='btn btn-sm btn-outline-secondary' href='edit.php?id=".$row['account_no']."'>Edit</a>
                  <a class='btn btn-sm btn-danger' href='delete.php?id=".$row['account_no']."' onclick='return confirm(\"Delete?\")'>Delete</a>
                </td>";
          echo "</tr>";
      }
      ?>
    </tbody>
  </table>
</div>
<?php include __DIR__ . "/../includes/footer.php"; ?>
