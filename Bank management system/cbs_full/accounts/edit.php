<?php
require_once __DIR__ . "/../config/db.php";
session_start();

// If not logged in
if(!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

// Validate ID
$account_no = intval($_GET['id'] ?? 0);
if ($account_no <= 0) {
    header("Location: view.php");
    exit;
}

// On form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $type = trim($_POST['type']);
    $balance = floatval($_POST['balance']);
    $status = trim($_POST['status']);

    $stmt = $conn->prepare("UPDATE accounts SET type=?, balance=?, status=? WHERE account_no=?");
    $stmt->bind_param("sdsi", $type, $balance, $status, $account_no);

    if ($stmt->execute()) {
        audit_log($conn, "Update Account", "accounts", $_SESSION['user'], "AccountNo: $account_no");
        header("Location: view.php");
        exit;
    } else {
        $error = "Error updating account: " . $stmt->error;
    }
}

// Fetch existing account details
$stmt = $conn->prepare("SELECT customer_id, type, balance, status FROM accounts WHERE account_no=? LIMIT 1");
$stmt->bind_param("i", $account_no);
$stmt->execute();
$stmt->bind_result($customer_id, $type, $balance, $status);
$stmt->fetch();
$stmt->close();

// Fetch customer name
$stmt2 = $conn->prepare("SELECT name FROM customers WHERE customer_id=?");
$stmt2->bind_param("i", $customer_id);
$stmt2->execute();
$stmt2->bind_result($customer_name);
$stmt2->fetch();
$stmt2->close();

include __DIR__ . "/../includes/header.php";
?>

<div class="col-md-10 p-4">
    <h4 class="text-primary">Edit Account</h4>

    <?php if(!empty($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" class="mt-3">

        <div class="mb-3">
            <label class="form-label">Customer</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($customer_name) ?>" disabled>
        </div>

        <div class="mb-3">
            <label class="form-label">Account Type</label>
            <input type="text" name="type" class="form-control" value="<?= htmlspecialchars($type) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Balance</label>
            <input type="number" name="balance" class="form-control" step="0.01" value="<?= htmlspecialchars($balance) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-control" required>
                <option value="Active"   <?= $status == "Active" ? "selected" : "" ?>>Active</option>
                <option value="Blocked"  <?= $status == "Blocked" ? "selected" : "" ?>>Blocked</option>
                <option value="Closed"   <?= $status == "Closed" ? "selected" : "" ?>>Closed</option>
            </select>
        </div>

        <button class="btn btn-primary">Update Account</button>
        <a href="view.php" class="btn btn-secondary ms-2">Cancel</a>
    </form>
</div>

<?php include __DIR__ . "/../includes/footer.php"; ?>
