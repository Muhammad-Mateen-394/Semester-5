<?php
require_once "../config/db.php";
session_start();

if(!isset($_SESSION['customer_id'])) {
    header("Location: customer_login.php");
    exit;
}

// Handle deposit BEFORE any HTML output
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $acc_no = $_POST['account_no'] ?? null;
    $amount = $_POST['amount'] ?? null;

    if (!$acc_no || !$amount || $amount <= 0) {
        die("Invalid deposit request.");
    }

    $conn->begin_transaction();

    try {

        // 1. Update balance
        $stmt = $conn->prepare(
            "UPDATE accounts SET balance = balance + ? 
             WHERE account_no = ? AND customer_id = ?"
        );
        $stmt->bind_param("dii", $amount, $acc_no, $_SESSION['customer_id']);
        $stmt->execute();

        // 2. Log transaction
        $stmt = $conn->prepare(
            "INSERT INTO transactions (from_account, to_account, amount, type) 
             VALUES (NULL, ?, ?, 'deposit')"
        );
        $stmt->bind_param("id", $acc_no, $amount);
        $stmt->execute();

        $conn->commit();

        header("Location: customer_transactions.php");
        exit;

    } catch (Exception $e) {
        $conn->rollback();
        die("Deposit failed: " . $e->getMessage());
    }
}

include "../includes/customer_header.php";
include "../includes/customer_sidebar.php";
?>

<div class="content-area" id="content-area">

    <div class="card p-4 shadow-sm">
        <h4 class="text-primary">Deposit Amount</h4>

        <form method="POST">

            <div class="mb-3">
                <label>Account</label>
                <select name="account_no" class="form-control" required>
                    <?php
                    // Customers should only see their own accounts
                    $stmt = $conn->prepare(
                        "SELECT account_no, type, balance 
                         FROM accounts 
                         WHERE customer_id = ?"
                    );
                    $stmt->bind_param("i", $_SESSION['customer_id']);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    while ($a = $result->fetch_assoc()) {
                        echo "<option value='{$a['account_no']}'>
                                {$a['type']} - ACC# {$a['account_no']} (Balance: {$a['balance']})
                              </option>";
                    }
                    ?>
                </select>
            </div>

            <div class="mb-3">
                <label>Amount</label>
                <input type="number" name="amount" class="form-control" required min="1" step="0.01">
            </div>

            <button class="btn btn-primary">Deposit</button>
        </form>

    </div>
</div>

<?php include "../includes/footer.php"; ?>
