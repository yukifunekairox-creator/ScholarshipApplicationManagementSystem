<?php
require_once '../includes/functions.php';

if (isAdmin()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $admin = run($conn, "SELECT * FROM admins WHERE username = ?", 's', array($username))->get_result()->fetch_assoc();

    if ($admin && password_verify($password, $admin['password'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $admin['admin_id'];
        $_SESSION['admin_name'] = $admin['full_name'];
        header('Location: dashboard.php');
        exit;
    }
    $error = 'Wrong username or password.';
}

$base = '../';
$pageTitle = 'Staff Login';
include '../includes/header.php';
?>
<div class="row justify-content-center">
  <div class="col-md-5 col-lg-4">
    <div class="card p-4 mt-3">
      <div class="text-center mb-3">
        <div class="icon-box mx-auto mb-2"><i class="bi bi-shield-lock"></i></div>
        <h4>Staff Login</h4>
        <p class="text-muted small">For scholarship office staff only.</p>
      </div>
      <?php if ($error != ''): ?>
        <div class="alert alert-danger py-2"><?= clean($error) ?></div>
      <?php endif; ?>
      <form method="post">
        <div class="mb-3">
          <label class="form-label" for="username">Username</label>
          <input type="text" id="username" name="username" class="form-control" required autofocus>
        </div>
        <div class="mb-3">
          <label class="form-label" for="password">Password</label>
          <input type="password" id="password" name="password" class="form-control" required>
        </div>
        <button class="btn btn-brand w-100">Login</button>
      </form>
    </div>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
