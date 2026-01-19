<?php
require_once "../config/db.php";
session_start();
if(!isset($_SESSION['user'])) header("Location: ../auth/login.php");

$res = $conn->query("
    SELECT 
        t.*,

        fu.username AS from_username,
        tu.username AS to_username

    FROM transactions t

    LEFT JOIN accounts fa ON fa.account_no = t.from_account
    LEFT JOIN customers fu ON fu.customer_id = fa.customer_id   

    LEFT JOIN accounts ta ON ta.account_no = t.to_account
    LEFT JOIN customers tu ON tu.customer_id = ta.customer_id   

    ORDER BY t.ts DESC
");


include "../includes/header.php";
?>

<h4 class="mb-3">Transaction History</h4>

<table class="table table-striped shadow-sm">
    <thead>
        <tr><th>ID</th><th>From</th><th>To</th><th>Type</th><th>Amount</th><th>Timestamp</th></tr>
    </thead>

    <tbody>
        <?php while($t = $res->fetch_assoc()): ?>
        <tr>
            <td><?= $t['trans_id'] ?></td><td><?= $t['from_username'] ?? '-' ?></td>
<td><?= $t['to_username'] ?? '-' ?></td>
<td><?= ucfirst($t['type']) ?></td>
            <td><?= $t['amount'] ?></td><td><?= $t['ts'] ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php include "../includes/footer.php"; ?>