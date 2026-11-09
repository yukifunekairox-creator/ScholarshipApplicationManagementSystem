<?php
$base = '../';
if (!isset($pageTitle)) { $pageTitle = 'Admin'; }
$current = basename($_SERVER['PHP_SELF']);
$navItems = array(
    array('dashboard.php', 'bi-speedometer2', 'Dashboard', array('dashboard.php')),
    array('applications.php', 'bi-people', 'Applications', array('applications.php', 'application_view.php', 'application_edit.php')),
    array('scholarships.php', 'bi-award', 'Scholarships', array('scholarships.php', 'scholarship_form.php'))
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= clean($pageTitle) ?> | ScholarHub Admin</title>
  <link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body class="admin-body">

<aside class="sidebar d-none d-lg-flex">
  <div class="sidebar-brand"><i class="bi bi-mortarboard-fill me-2"></i>ScholarHub</div>
  <ul class="nav flex-column mt-3">
    <?php foreach ($navItems as $item): ?>
      <li class="nav-item">
        <a class="side-link <?= in_array($current, $item[3]) ? 'active' : '' ?>" href="<?= $item[0] ?>">
          <i class="bi <?= $item[1] ?> me-2"></i><?= $item[2] ?>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
  <div class="mt-auto">
    <a class="side-link" href="../index.php"><i class="bi bi-globe me-2"></i>View Website</a>
    <a class="side-link" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
  </div>
</aside>

<div class="offcanvas offcanvas-start sidebar-mobile" tabindex="-1" id="mobileMenu">
  <div class="offcanvas-header">
    <div class="sidebar-brand p-0"><i class="bi bi-mortarboard-fill me-2"></i>ScholarHub</div>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body d-flex flex-column">
    <?php foreach ($navItems as $item): ?>
      <a class="side-link <?= in_array($current, $item[3]) ? 'active' : '' ?>" href="<?= $item[0] ?>">
        <i class="bi <?= $item[1] ?> me-2"></i><?= $item[2] ?>
      </a>
    <?php endforeach; ?>
    <div class="mt-auto">
      <a class="side-link" href="../index.php"><i class="bi bi-globe me-2"></i>View Website</a>
      <a class="side-link" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
    </div>
  </div>
</div>

<div class="admin-main">
  <div class="admin-topbar d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center">
      <button class="btn btn-sm btn-outline-secondary d-lg-none me-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu">
        <i class="bi bi-list fs-5"></i>
      </button>
      <h5 class="mb-0"><?= clean($pageTitle) ?></h5>
    </div>
    <div class="text-muted small d-none d-sm-block"><i class="bi bi-person-circle"></i> <?= clean($_SESSION['admin_name']) ?></div>
  </div>
  <div class="p-3 p-md-4">
  <?php if (isset($_SESSION['flash'])): ?>
    <div class="alert alert-<?= $_SESSION['flash']['type'] ?> alert-dismissible fade show">
      <?= clean($_SESSION['flash']['message']) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['flash']); ?>
  <?php endif; ?>
