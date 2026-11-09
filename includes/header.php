<?php
if (!isset($base)) { $base = ''; }
if (!isset($pageTitle)) { $pageTitle = 'Scholarship System'; }
$current = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= clean($pageTitle) ?> | ScholarHub</title>
  <link href="<?= $base ?>assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="<?= $base ?>assets/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-white site-nav">
  <div class="container">
    <a class="navbar-brand fw-bold brand-text" href="<?= $base ?>index.php"><i class="bi bi-mortarboard-fill me-2"></i>ScholarHub</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#siteMenu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="siteMenu">
      <ul class="navbar-nav ms-auto align-items-lg-center">
        <li class="nav-item"><a class="nav-link <?= $current == 'index.php' ? 'active' : '' ?>" href="<?= $base ?>index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= $base ?>index.php#scholarships">Scholarships</a></li>
        <li class="nav-item"><a class="nav-link <?= $current == 'track.php' ? 'active' : '' ?>" href="<?= $base ?>track.php">Track Application</a></li>
        <li class="nav-item">
          <a class="btn btn-sm btn-outline-brand ms-lg-2 mt-2 mt-lg-0" href="<?= $base ?>admin/<?= isAdmin() ? 'dashboard.php' : 'login.php' ?>">
            <i class="bi bi-shield-lock"></i> <?= isAdmin() ? 'Dashboard' : 'Staff Login' ?>
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>
<main class="container py-4">
<?php if (isset($_SESSION['flash'])): ?>
  <div class="alert alert-<?= $_SESSION['flash']['type'] ?> alert-dismissible fade show">
    <?= clean($_SESSION['flash']['message']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  <?php unset($_SESSION['flash']); ?>
<?php endif; ?>
