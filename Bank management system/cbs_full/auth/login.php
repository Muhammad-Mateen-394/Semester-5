<?php
require_once "../config/db.php";
session_start();

$error_admin = "";
$error_customer = "";

// -----------------------------
// ADMIN LOGIN
// -----------------------------
if (isset($_POST['admin_login'])) {

    $username = trim($_POST['admin_username']);
    $password = $_POST['admin_password'];

    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE username=? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($id, $hash, $role);

    if ($stmt->fetch() && password_verify($password, $hash)) {

        $_SESSION['user'] = $username;
        $_SESSION['role'] = $role;

        header("Location: ../index.php");
        exit;

    } else {
        $error_admin = "Invalid admin username or password.";
    }

    $stmt->close();
}


// -----------------------------
// CUSTOMER LOGIN
// -----------------------------
if (isset($_POST['customer_login'])) {

    $username = trim($_POST['customer_username']);
    $password = $_POST['customer_password'];

    $stmt = $conn->prepare("SELECT customer_id, name, password FROM customers WHERE username = ? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($id, $name, $hash);

    if ($stmt->fetch() && password_verify($password, $hash)) {

        $_SESSION['customer_id'] = $id;
        $_SESSION['customer_name'] = $name;

        header("Location: ../customers/customer_dashboard.php");
        exit;

    } else {
        $error_customer = "Invalid customer username or password.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CBS Unified Login</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    body {
      background: linear-gradient(to bottom right, #e9f2ff, #f7faff);
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Segoe UI', sans-serif;
    }
    .login-box {
      width: 420px;
      padding: 25px;
      background: white;
      border-radius: 12px;
      box-shadow: 0 6px 25px rgba(0, 0, 0, 0.1);
    }
  </style>
</head>

<body>

<div class="login-box">
    <h2 class="text-center text-primary mb-3">Core Banking System</h2>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-3" id="loginTabs">
      <li class="nav-item">
        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#adminTab">Admin Login</button>
      </li>
      <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#customerTab">Customer Login</button>
      </li>
    </ul>

    <div class="tab-content">

      <!-- =========================
           ADMIN LOGIN TAB
      ========================== -->
      <div class="tab-pane fade show active" id="adminTab">

        <?php if ($error_admin): ?>
          <div class="alert alert-danger py-2"><?= $error_admin ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="admin_login">

            <div class="mb-3">
                <label class="form-label fw-semibold">Username</label>
                <input type="text" name="admin_username" class="form-control" required autocomplete="off">
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Password</label>
                <div class="input-group">
                    <input type="password" name="admin_password" id="admin_password" class="form-control" required>
                    <span class="input-group-text" style="cursor:pointer;" onclick="togglePass('admin_password','icon1')">
                        <i class="bi bi-eye-slash" id="icon1"></i>
                    </span>
                </div>
            </div>

            <button class="btn btn-primary w-100 mt-2">Login as Admin</button>
        </form>
      </div>


      <!-- =========================
           CUSTOMER LOGIN TAB
      ========================== -->
      <div class="tab-pane fade" id="customerTab">

        <?php if ($error_customer): ?>
          <div class="alert alert-danger py-2"><?= $error_customer ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="customer_login">

            <div class="mb-3">
                <label class="form-label fw-semibold">Username</label>
                <input type="text" name="customer_username" class="form-control" required autocomplete="off">
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Password</label>
                <div class="input-group">
                    <input type="password" name="customer_password" id="customer_password" class="form-control" required>
                    <span class="input-group-text" style="cursor:pointer;" onclick="togglePass('customer_password','icon2')">
                        <i class="bi bi-eye-slash" id="icon2"></i>
                    </span>
                </div>
            </div>

            <button class="btn btn-success w-100 mt-2">Login as Customer</button>
        </form>
      </div>

    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
function togglePass(field, iconId) {
    const input = document.getElementById(field);
    const icon = document.getElementById(iconId);

    if (input.type === "password") {
        input.type = "text";
        icon.classList.replace("bi-eye-slash", "bi-eye");
    } else {
        input.type = "password";
        icon.classList.replace("bi-eye", "bi-eye-slash");
    }
}
</script>

</body>
</html>
