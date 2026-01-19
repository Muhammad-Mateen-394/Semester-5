<?php
require_once __DIR__ . "/../config/db.php";
session_start(); if(!isset($_SESSION['user'])) header("Location: ../auth/login.php");
include __DIR__ . "/../includes/header.php";

$res = $conn->query("SELECT * FROM auditlog ORDER BY ts DESC LIMIT 200");
?>
<div class="col-md-10 p-4">
  <h4>Audit Logs</h4>
  <table class="table mt-3">
    <thead><tr><th>#</th><th>Operation</th><th>Table</th><th>User</th><th>Details</th><th>When</th></tr></thead>
    <tbody>
      <?php while($r = $res->fetch_assoc()){
        echo "<tr>";
        echo "<td>".$r['log_id']."</td>";
        echo "<td>".htmlspecialchars($r['operation'])."</td>";
        echo "<td>".htmlspecialchars($r['table_affected'])."</td>";
        echo "<td>".htmlspecialchars($r['user_name'])."</td>";
        echo "<td>".htmlspecialchars($r['details'])."</td>";
        echo "<td>".$r['ts']."</td>";
        echo "</tr>";
      } ?>
    </tbody>
  </table>
</div>
<?php include __DIR__ . "/../includes/footer.php"; ?>
