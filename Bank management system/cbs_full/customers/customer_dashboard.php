<?php
session_start();
if (!isset($_SESSION['customer_id'])) {
    header("Location: customer_login.php");
    exit;
}

require "../config/db.php";
include "../includes/customer_header.php";
include "../includes/customer_sidebar.php";

$cid = $_SESSION['customer_id'];

// Get ALL customer accounts
$accounts = $conn->query("SELECT * FROM accounts WHERE customer_id = $cid");

// Calculate total balance
$total_balance = 0;
while ($acc = $accounts->fetch_assoc()) {
    $total_balance += $acc['balance'];
}

// Reset pointer to fetch again later
$accounts = $conn->query("SELECT * FROM accounts WHERE customer_id = $cid");

// Fetch last 5 transactions
$acc_ids = [];
while ($row = $accounts->fetch_assoc()) {
    $acc_ids[] = $row['account_no'];
}
$id_list = implode(",", $acc_ids);

$transactions = $conn->query("
    SELECT t.*, 
           fa.account_no AS from_acc,
           ta.account_no AS to_acc
    FROM transactions t
    LEFT JOIN accounts fa ON fa.account_no = t.from_account
    LEFT JOIN accounts ta ON ta.account_no = t.to_account
    WHERE t.from_account IN ($id_list) OR t.to_account IN ($id_list)
    ORDER BY t.ts DESC
    LIMIT 5
");

// Reset accounts again for displaying
$accounts = $conn->query("SELECT * FROM accounts WHERE customer_id = $cid");
?>

<div class="content-area">

<div class="container mt-4">

    <!-- Greeting -->
    <h2 class="mb-4 text-primary">Welcome, <?= $_SESSION['customer_name'] ?></h2>

    <!-- Total Balance Card -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm p-3 bg-primary text-white">
                <h5>Total Balance</h5>
                <h2>PKR <?= number_format($total_balance, 2) ?></h2>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-md-8">
            <div class="card shadow-sm p-3">
                <h5>Quick Actions</h5>
                <div class="mt-2">
                    <a href="deposit.php" class="btn btn-success me-2">Deposit</a>
                    <a href="withdraw.php" class="btn btn-danger me-2">Withdraw</a>
                    <a href="transfer.php" class="btn btn-warning me-2">Transfer</a>
                    <a href="customer_transactions.php" class="btn btn-primary">View All Transactions</a>
                </div>
            </div>
        </div>
    </div>

    <!-- All Accounts -->
    <div class="card shadow-sm p-3 mb-4">
        <h4 class="mb-3 text-primary">Your Accounts</h4>

        <div class="row">
            <?php while ($acc = $accounts->fetch_assoc()): ?>
                <div class="col-md-4 mb-3">
                    <div class="card p-3 shadow-sm border-primary">
                        <h5 class="text-primary"><?= $acc['type'] ?> Account</h5>
                        <p class="mb-1"><strong>Account Number:</strong> <?= $acc['account_no'] ?></p>
                        <p class="mb-1"><strong>Balance:</strong> PKR <?= number_format($acc['balance'], 2) ?></p>
                        <p class="mb-1"><strong>Status:</strong> Active</p>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="card shadow-sm p-3">
        <h4 class="mb-3 text-primary">Recent Transactions</h4>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Date</th>
                </tr>
            </thead>

            <tbody>
                <?php while ($t = $transactions->fetch_assoc()): ?>
                    <tr>
                        <td><?= $t['trans_id'] ?></td>
                        <td><?= $t['from_acc'] ?? '-' ?></td>
                        <td><?= $t['to_acc'] ?? '-' ?></td>
                        <td><?= ucfirst($t['type']) ?></td>
                        <td>PKR <?= number_format($t['amount'], 2) ?></td>
                        <td><?= $t['ts'] ?></td>
                    </tr>
                <?php endwhile; ?>

                <?php if ($transactions->num_rows == 0): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted">No transactions yet</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

    </div>

</div>
</div>

<?php include "../includes/footer.php"; ?>
