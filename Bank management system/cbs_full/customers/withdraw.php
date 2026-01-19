<?php

session_start();
if(!isset($_SESSION['customer_id'])) {
    header("Location: customer_login.php");
    exit;
}

require "../config/db.php";
include "../includes/customer_header.php";
include "../includes/customer_sidebar.php";

$cid = $_SESSION['customer_id'];

if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $acc = $_POST['account_no'];
    $amount = $_POST['amount'];

    // Check account belongs to logged-in customer
    $stmt = $conn->prepare("SELECT balance FROM accounts WHERE account_no = ? AND customer_id = ?");
    $stmt->bind_param("ii", $acc, $cid);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if(!$row){
        $error = "Invalid account! You cannot withdraw from this account.";
    } else {

        $balance = $row['balance'];

        if($balance < $amount){
            $error = "Insufficient funds!";
        } else {

            // ---------------------------------------------
            //           START TCL TRANSACTION
            // ---------------------------------------------

            $conn->begin_transaction();

            try {

                // 1. Deduct from customer account
                $stmt = $conn->prepare(
                    "UPDATE accounts SET balance = balance - ? 
                     WHERE account_no = ? AND customer_id = ?"
                );
                $stmt->bind_param("dii", $amount, $acc, $cid);
                $stmt->execute();

                // 2. Log transaction
                $stmt = $conn->prepare(
                    "INSERT INTO transactions (from_account, to_account, amount, type) 
                     VALUES (?, NULL, ?, 'withdraw')"
                );
                $stmt->bind_param("id", $acc, $amount);
                $stmt->execute();

                // 3. COMMIT TCL
                $conn->commit();

                header("Location: customer_transactions.php");
                exit;

            } catch (Exception $e) {

                // 4. ROLLBACK if ANY error happens
                $conn->rollback();
                $error = "Withdrawal failed! Transaction rolled back.";
            }
        }
    }
}

?>
<div class="content-area">

<div class="card p-4 shadow-sm">
  <h4 class="mb-3 text-primary">Withdraw Amount</h4>

  <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

  <form method="POST">

      <div class="mb-3">
          <label>Account</label>
          <select name="account_no" class="form-control" required>
              <?php
              $res = $conn->query("SELECT * FROM accounts WHERE customer_id = $cid");
              while($a = $res->fetch_assoc()) {
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

      <button class="btn btn-danger">Withdraw</button>
  </form>
</div>

</div>

<?php include "../includes/footer.php"; ?>
