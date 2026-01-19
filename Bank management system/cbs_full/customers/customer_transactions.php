<?php
session_start();
if(!isset($_SESSION['customer_id'])) {
    header("Location: customer_login.php");
    exit;
}

require_once "../config/db.php";

include "../includes/customer_header.php";
include "../includes/customer_sidebar.php";

$cid = $_SESSION['customer_id'];

// Fetch customer accounts
$acc_res = $conn->query("SELECT * FROM accounts WHERE customer_id = $cid");

$account_ids = [];
while($a = $acc_res->fetch_assoc()) {
    $account_ids[] = $a['account_no'];
}

if (empty($account_ids)) {
    die("You have no accounts.");
}

$ids = implode(",", $account_ids);

// Fetch transactions with owner names
$trans = $conn->query("
    SELECT 
        t.*,

        fu.username AS from_username,
        tu.username AS to_username

    FROM transactions t

    LEFT JOIN accounts fa ON fa.account_no = t.from_account
    LEFT JOIN customers fu ON fu.customer_id = fa.customer_id   -- From user

    LEFT JOIN accounts ta ON ta.account_no = t.to_account
    LEFT JOIN customers tu ON tu.customer_id = ta.customer_id   -- To user

    WHERE t.from_account IN ($ids)
       OR t.to_account IN ($ids)

    ORDER BY t.ts DESC
");

?>
<div class="content-area">

<div class="container mt-4">
    <div class="card p-4 shadow-sm">
        <h3 class="text-primary mb-4">
            <?= $_SESSION['customer_name'] ?> — Your Transactions
        </h3>

        <table class="table table-striped shadow-sm">
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
                <?php while($t = $trans->fetch_assoc()): ?>
                    <tr>
                        <td><?= $t['trans_id'] ?></td>

                        <td><?= $t['from_username'] ?? '-' ?></td>
<td><?= $t['to_username'] ?? '-' ?></td>


                        <td><?= ucfirst($t['type']) ?></td>
                        <td><?= number_format($t['amount'], 2) ?></td>
                        <td><?= $t['ts'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

    </div>
</div>
</div>
