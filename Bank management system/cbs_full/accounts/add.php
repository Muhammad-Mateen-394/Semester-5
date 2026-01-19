<?php
require_once __DIR__ . "/../config/db.php";
session_start(); if(!isset($_SESSION['user'])) header("Location: ../auth/login.php");

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $customer_id = intval($_POST['customer_id']);
    $type = $_POST['type'];
    $balance = floatval($_POST['balance']);

    $stmt = $conn->prepare("INSERT INTO accounts (customer_id, type, balance) VALUES (?, ?, ?)");
    $stmt->bind_param("isd", $customer_id, $type, $balance);
    if($stmt->execute()){
        audit_log($conn, 'Create Account', 'accounts', $_SESSION['user'], "Account for CustomerID:$customer_id");
        header("Location: view.php");
        exit;
    } else {
        $error = $stmt->error;
    }
}

// get customers for dropdown
$customers = $conn->query("SELECT customer_id, name FROM customers ORDER BY name");
include __DIR__ . "/../includes/header.php";
?>
<div class="col-md-10 p-4">
  <h4>Open Account</h4>
  <?php if(!empty($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
  <form method="post">
    <div class="mb-3">
      <label>Customer</label>
      <select name="customer_id" class="form-control" required>
        <?php while($c = $customers->fetch_assoc()){ echo "<option value='".$c['customer_id']."'>".htmlspecialchars($c['name'])."</option>"; } ?>
      </select>
    </div>
    <div class="mb-3"><label>Account Type</label><input name="type" class="form-control" required></div>
    <div class="mb-3"><label>Initial Balance</label><input name="balance" type="number" step="0.01" value="0" class="form-control"></div>
    <button class="btn btn-primary">Create</button>
    <a href="view.php" class="btn btn-outline-secondary">Cancel</a>
  </form>
</div>
<?php include __DIR__ . "/../includes/footer.php"; ?>
