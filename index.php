<?php
require_once 'includes/functions.php';
$pageTitle = 'Home';

$sql = "SELECT s.*, (SELECT COUNT(*) FROM applications a WHERE a.scholarship_id = s.scholarship_id AND a.status = 'Approved') AS approved
        FROM scholarships s ORDER BY s.status, s.deadline";
$scholarships = $conn->query($sql);

$openCount = $conn->query("SELECT COUNT(*) AS total FROM scholarships WHERE status = 'Open' AND deadline >= CURDATE()")->fetch_assoc()['total'];
$grantedCount = $conn->query("SELECT COUNT(*) AS total FROM applications WHERE status = 'Approved'")->fetch_assoc()['total'];

include 'includes/header.php';
?>
<div class="hero text-center mb-5">
  <h1 class="fw-bold">Find a scholarship and apply online</h1>
  <p class="lead text-muted mb-4">Check the requirements, submit your application, and follow its progress with your reference code.</p>
  <a href="#scholarships" class="btn btn-brand btn-lg me-2"><i class="bi bi-search"></i> Browse Scholarships</a>
  <a href="track.php" class="btn btn-outline-brand btn-lg">Track My Application</a>
  <div class="row justify-content-center mt-4 g-3">
    <div class="col-6 col-md-3"><div class="fs-3 fw-bold text-dark"><?= $openCount ?></div><div class="small text-muted">Open scholarships</div></div>
    <div class="col-6 col-md-3"><div class="fs-3 fw-bold text-dark"><?= $grantedCount ?></div><div class="small text-muted">Students approved</div></div>
  </div>
</div>

<div class="row g-4 mb-5 text-center">
  <div class="col-md-4">
    <div class="icon-box mx-auto mb-3"><i class="bi bi-card-checklist"></i></div>
    <h6>1. Choose a scholarship</h6>
    <p class="text-muted small">Read the benefit, slots and GWA requirement.</p>
  </div>
  <div class="col-md-4">
    <div class="icon-box mx-auto mb-3"><i class="bi bi-pencil-square"></i></div>
    <h6>2. Fill in the form</h6>
    <p class="text-muted small">Add your details and upload a supporting document.</p>
  </div>
  <div class="col-md-4">
    <div class="icon-box mx-auto mb-3"><i class="bi bi-bell"></i></div>
    <h6>3. Follow the result</h6>
    <p class="text-muted small">Use your reference code to see if you were approved.</p>
  </div>
</div>

<h4 class="mb-3" id="scholarships">Available Scholarships</h4>
<div class="row g-4">
  <?php while ($s = $scholarships->fetch_assoc()): ?>
    <?php $open = isOpenForApplying($s); $left = max(0, $s['slots'] - $s['approved']); ?>
    <div class="col-lg-6">
      <div class="card scholar-card p-4 h-100">
        <div class="d-flex justify-content-between align-items-start mb-2">
          <h5 class="mb-0"><?= clean($s['scholarship_name']) ?></h5>
          <?= statusBadge($open ? 'Open' : 'Closed') ?>
        </div>
        <div class="text-muted small mb-2"><i class="bi bi-building"></i> <?= clean($s['sponsor']) ?></div>
        <p class="text-muted"><?= clean($s['description']) ?></p>
        <div class="row small mb-3">
          <div class="col-6 col-md-3"><div class="text-muted">Benefit</div><div class="amount"><?= peso($s['benefit_amount']) ?></div></div>
          <div class="col-6 col-md-3"><div class="text-muted">Slots left</div><strong><?= $left ?> of <?= $s['slots'] ?></strong></div>
          <div class="col-6 col-md-3"><div class="text-muted">GWA needed</div><strong><?= number_format($s['max_gwa'], 2) ?> or better</strong></div>
          <div class="col-6 col-md-3"><div class="text-muted">Deadline</div><strong><?= formatDate($s['deadline']) ?></strong></div>
        </div>
        <div class="mt-auto">
          <?php if ($open && $left > 0): ?>
            <a href="apply.php?id=<?= $s['scholarship_id'] ?>" class="btn btn-brand">Apply Now</a>
          <?php elseif ($open): ?>
            <button class="btn btn-secondary" disabled>No slots left</button>
          <?php else: ?>
            <button class="btn btn-secondary" disabled>Applications closed</button>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <?php endwhile; ?>
</div>
<?php include 'includes/footer.php'; ?>
