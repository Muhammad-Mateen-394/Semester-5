<?php
require_once __DIR__ . "/../config/db.php";
session_start();
if(!isset($_SESSION['user'])) header("Location: ../auth/login.php");

$id = $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM customers WHERE customer_id=? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$customer = $stmt->get_result()->fetch_assoc();

if(!$customer){
    die("Customer not found!");
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $name = trim($_POST['name']);
    $cnic = trim($_POST['cnic']);
    $contact = trim($_POST['contact']);
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);

    $update = $conn->prepare("
        UPDATE customers 
        SET name=?, cnic=?, contact=?, email=?, username=? 
        WHERE customer_id=?
    ");
    $update->bind_param("sssssi", $name, $cnic, $contact, $email, $username, $id);

    if($update->execute()){
        audit_log($conn, "Edit Customer", "customers", $_SESSION['user'], "Customer ID: $id");
        header("Location: view.php");
        exit;
    } else {
        $error = $update->error;
    }
}

include "../includes/header.php";
?>

<div class="card p-4 shadow-sm">
    <h4 class="mb-3 text-primary">Edit Customer</h4>

    <?php if(!empty($error)) : ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">

        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input name="name" class="form-control" value="<?= $customer['name'] ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">CNIC</label>
            <input name="cnic" class="form-control" value="<?= $customer['cnic'] ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Contact</label>
            <input name="contact" class="form-control" value="<?= $customer['contact'] ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input name="email" class="form-control" value="<?= $customer['email'] ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Username</label>
            <input name="username" class="form-control" value="<?= $customer['username'] ?>" required>
        </div>

        <button class="btn btn-primary">Update</button>
        <a href="view.php" class="btn btn-outline-secondary ms-2">Cancel</a>
    </form>
</div>

<?php include "../includes/footer.php"; ?>
