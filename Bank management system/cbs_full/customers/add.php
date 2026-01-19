<?php
require_once __DIR__ . "/../config/db.php";
session_start(); 
if(!isset($_SESSION['user'])) header("Location: ../auth/login.php");

if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $name = trim($_POST['name']);
    $cnic = trim($_POST['cnic']);
    $contact = trim($_POST['contact']);
    $email = trim($_POST['email']);

    // Auto-generate username & password
    $username = strtolower(str_replace(' ', '', $name)) . rand(100,999);
    $plain_password = substr($cnic, -4); // last 4 digits of CNIC
    $password = password_hash($plain_password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO customers (name, cnic, contact, email, username, password) 
                            VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $name, $cnic, $contact, $email, $username, $password);

    if($stmt->execute()){
        audit_log($conn, 'Create Customer', 'customers', $_SESSION['user'], "Customer: $name");

        header("Location: view.php");
        exit;
    } else {
        $error = $stmt->error;
    }
}

include __DIR__ . "/../includes/header.php";
?>

<div class="card p-4 shadow-sm">
    <h4 class="mb-3 text-primary">Add Customer</h4>

    <?php if(!empty($error)) : ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="post">

        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">CNIC</label>
            <input name="cnic" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Contact</label>
            <input name="contact" class="form-control" required>
        </div>
        <div class="mb-3">
    <label class="form-label">Email</label>
    <input name="email" class="form-control" required>
</div>

        <button class="btn btn-primary">Save</button>
        <a href="view.php" class="btn btn-outline-secondary ms-2">Cancel</a>
    </form>
</div>

<?php include __DIR__ . "/../includes/footer.php"; ?>
