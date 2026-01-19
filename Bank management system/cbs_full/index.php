<?php
require_once "config/db.php";
session_start();
if(!isset($_SESSION['user'])) header("Location: auth/login.php");
include "includes/header.php";
?>

<div class="col-md-10 p-4">
  <h3 class="text-primary">Dashboard</h3>

  <div class="row mt-4">

    <!-- Total Customers -->
    <div class="col-md-4 mb-3">
      <div class="card shadow-sm p-3">
        <div class="card-body">
          <h6>Total Customers</h6>
          <?php
            $r = $conn->query("SELECT COUNT(*) AS cnt FROM customers")->fetch_assoc();
            echo "<h3 class='text-primary'>".$r['cnt']."</h3>";
          ?>
        </div>
      </div>
    </div>

    <!-- Total Accounts -->
    <div class="col-md-4 mb-3">
      <div class="card shadow-sm p-3">
        <div class="card-body">
          <h6>Total Accounts</h6>
          <?php 
            $r = $conn->query("SELECT COUNT(*) AS cnt FROM accounts")->fetch_assoc(); 
            echo "<h3>".$r['cnt']."</h3>"; 
          ?>
        </div>
      </div>
    </div>

    <!-- System Balance -->
    <div class="col-md-4 mb-3">
      <div class="card shadow-sm p-3">
        <div class="card-body">
          <h6>System Balance</h6>
          <?php 
            $r = $conn->query("SELECT COALESCE(SUM(balance),0) AS total FROM accounts")->fetch_assoc(); 
            echo "<h3>Rs ".number_format($r['total'],2)."</h3>"; 
          ?>
        </div>
      </div>
    </div>

    <!-- Top 5 Customers by Total Balance -->
    <div class="col-md-12 mb-3">
      <div class="card shadow-sm p-3">
        <div class="card-body">

          <h4 class="mb-3">Customers</h4>

          <table class="table table-sm table-striped">
            <thead>
              <tr>
                <th>#</th>
                <th>Username</th>
                <th>Total Balance</th>
              </tr>
            </thead>
            <tbody>
              <?php
                $top = $conn->query("
                  SELECT c.username, COALESCE(SUM(a.balance),0) AS total_bal
                  FROM customers c
                  LEFT JOIN accounts a ON c.customer_id = a.customer_id
                  GROUP BY c.customer_id
                  ORDER BY total_bal DESC
                  LIMIT 5
                ");

                $i = 1;
                while($row = $top->fetch_assoc()):
              ?>
              <tr>
                <td><?= $i++; ?></td>
                <td><?= $row['username']; ?></td>
                <td>Rs <?= number_format($row['total_bal'], 2); ?></td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>

        </div>
      </div>
    </div>

  </div>
</div>

<?php include "includes/footer.php"; ?>
