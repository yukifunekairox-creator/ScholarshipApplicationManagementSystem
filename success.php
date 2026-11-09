<?php
require_once 'includes/functions.php';

$ref = isset($_GET['ref']) ? trim($_GET['ref']) : '';
$sql = "SELECT a.*, s.scholarship_name FROM applications a
        JOIN scholarships s ON a.scholarship_id = s.scholarship_id WHERE a.reference_code = ?";
$app = run($conn, $sql, 's', array($ref))->get_result()->fetch_assoc();

$pageTitle = 'Application Submitted';
include 'includes/header.php';
?>
<div class="row justify-content-center">
  <div class="col-lg-6">
    <?php if (!$app): ?>
      <div class="alert alert-warning">We could not find that application. <a href="track.php">Track an application</a>.</div>
    <?php else: ?>
      <div class="card p-4 text-center">
        <div class="text-success fs-1"><i class="bi bi-check-circle-fill"></i></div>
        <h4>Application submitted!</h4>
        <p class="text-muted">Keep this reference code. You need it to track your application.</p>
        <div class="ref-code mb-3"><?= clean($app['reference_code']) ?></div>
        <ul class="list-group list-group-flush text-start mb-3">
          <li class="list-group-item d-flex justify-content-between"><span>Applicant</span><strong><?= clean($app['full_name']) ?></strong></li>
          <li class="list-group-item d-flex justify-content-between"><span>Scholarship</span><strong><?= clean($app['scholarship_name']) ?></strong></li>
          <li class="list-group-item d-flex justify-content-between"><span>Status</span><?= statusBadge($app['status']) ?></li>
        </ul>
        <div class="d-flex gap-2 justify-content-center">
          <button class="btn btn-outline-brand" onclick="window.print()"><i class="bi bi-printer"></i> Print</button>
          <a href="track.php" class="btn btn-brand">Track Application</a>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
