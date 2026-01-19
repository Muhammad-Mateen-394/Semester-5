<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm py-2">
  <div class="container-fluid">
    <span class="navbar-brand fw-bold text-primary fs-4">
      <i class="bi bi-bank"></i> CBS
    </span>

    <div class="ms-auto d-flex align-items-center gap-3">
      <span class="fw-semibold text-muted">
        <i class="bi bi-person-circle"></i> <?= $_SESSION['user'] ?>
      </span>

      <a href="/cbs_full_project/cbs_full/auth/logout.php" class="btn btn-outline-danger btn-sm">
        <i class="bi bi-box-arrow-right"></i> Logout
      </a>
    </div>
  </div>
</nav>
