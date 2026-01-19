<?php
require_once "../config/db.php";
session_start();

if(!isset($_SESSION['customer_id'])) {
    header("Location: customer_login.php");
    exit;
}

include "../includes/customer_header.php";
include "../includes/customer_sidebar.php";

$cid = $_SESSION['customer_id'];

if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $from = $_POST['from_acc'];
    $to = $_POST['to_acc'];
    $amount = $_POST['amount'];

    if($from == $to){
        $error = "From and To account cannot be same!";
    } else {

        // verify "from" account belongs to customer
        $stmt = $conn->prepare(
            "SELECT balance FROM accounts WHERE account_no = ? AND customer_id = ?"
        );
        $stmt->bind_param("ii", $from, $cid);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if(!$row){
            $error = "You cannot transfer from an account that is not yours!";
        } else {

            $balance = $row['balance'];

            if($balance < $amount){
                $error = "Insufficient Funds!";
            } else {

                // -----------------------------------------------
                //        TCL TRANSACTION START
                // -----------------------------------------------

                $conn->begin_transaction();

                try {

                    // Deduct from sender
                    $stmt = $conn->prepare(
                        "UPDATE accounts SET balance = balance - ? WHERE account_no = ?"
                    );
                    $stmt->bind_param("di", $amount, $from);
                    $stmt->execute();

                    // Add to receiver
                    $stmt = $conn->prepare(
                        "UPDATE accounts SET balance = balance + ? WHERE account_no = ?"
                    );
                    $stmt->bind_param("di", $amount, $to);
                    $stmt->execute();

                    // Log transfer
                    $stmt = $conn->prepare(
                        "INSERT INTO transactions (from_account, to_account, amount, type)
                         VALUES (?, ?, ?, 'transfer')"
                    );
                    $stmt->bind_param("iid", $from, $to, $amount);
                    $stmt->execute();

                    // Commit the TCL transaction
                    $conn->commit();

                    header("Location: customer_transactions.php");
                    exit;

                } catch (Exception $e) {

                    // Rollback if ANY error happens
                    $conn->rollback();
                    $error = "Transfer failed! Transaction rolled back.";
                }
            }
        }
    }
}

?>
<div class="content-area">

<div class="card p-4 shadow-sm">
  <h4 class="mb-3 text-primary">Transfer Money</h4>

  <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

  <form method="POST">

      <!-- From Account (Customer Only) -->
      <div class="mb-3">
          <label>From Account</label>
          <select name="from_acc" class="form-control" required>
              <?php
              $stmt = $conn->prepare(
                  "SELECT account_no, type, balance 
                   FROM accounts 
                   WHERE customer_id = ?"
              );
              $stmt->bind_param("i", $cid);
              $stmt->execute();
              $res = $stmt->get_result();

              while($a = $res->fetch_assoc()) {
                  echo "<option value='{$a['account_no']}'>
                          {$a['type']} - ACC# {$a['account_no']} (Balance: {$a['balance']})
                        </option>";
              }
              ?>
          </select>
      </div>

      <!-- To Account (All Accounts) -->
      <div class="mb-3">
          <label>To Account</label>
          <select name="to_acc" class="form-control" required>
              <?php
              $res = $conn->query("
                  SELECT accounts.account_no, accounts.type, customers.name
                  FROM accounts
                  INNER JOIN customers ON accounts.customer_id = customers.customer_id
              ");

              while($a = $res->fetch_assoc()) {
                  echo "<option value='{$a['account_no']}'>
                          {$a['type']} - ACC# {$a['account_no']} (Owner: {$a['name']})
                        </option>";
              }
              ?>
          </select>
      </div>

      <!-- Amount -->
      <div class="mb-3">
          <label>Amount</label>
          <input type="number" name="amount" class="form-control" required min="1" step="0.01">
      </div>

      <button class="btn btn-primary">Transfer</button>

  </form>
</div>

</div>

<?php include "../includes/footer.php"; ?>
