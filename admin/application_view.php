<?php
require_once '../includes/functions.php';
requireAdmin();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$sql = "SELECT a.*, s.scholarship_name, s.slots, s.max_gwa, s.benefit_amount FROM applications a
        JOIN scholarships s ON a.scholarship_id = s.scholarship_id WHERE a.application_id = ?";
$app = run($conn, $sql, 'i', array($id))->get_result()->fetch_assoc();

if (!$app) {
    setFlash('danger', 'Application not found.');
    header('Location: applications.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $newStatus = $_POST['status'];
    $remarks = trim($_POST['remarks']);

    if (!in_array($newStatus, getStatuses())) {
        $error = 'Invalid status.';
    } elseif ($newStatus == 'Approved' && approvedCount($conn, $app['scholarship_id'], $id) >= $app['slots']) {
        $error = 'All ' . $app['slots'] . ' slots for this scholarship are already filled.';
    } else {
        run($conn, "UPDATE applications SET status = ?, remarks = ? WHERE application_id = ?", 'ssi', array($newStatus, $remarks, $id));
        setFlash('success', 'Application marked as ' . $newStatus . '.');
        header('Location: application_view.php?id=' . $id);
        exit;
    }
    $app['status'] = $newStatus;
    $app['remarks'] = $remarks;
}

$pageTitle = 'Review Application';
include '../includes/admin_header.php';
?>
<div class="mb-3"><a href="applications.php" class="text-decoration-none"><i class="bi bi-arrow-left"></i> Back to applications</a></div>
<div class="row g-3">
  <div class="col-lg-7">
    <div class="card p-4">
      <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
          <h5 class="mb-0"><?= clean($app['full_name']) ?></h5>
          <div class="text-muted small"><?= clean($app['reference_code']) ?> &middot; submitted <?= formatDate($app['created_at']) ?></div>
        </div>
        <?= statusBadge($app['status']) ?>
      </div>
      <div class="row g-3 small mb-3">
        <div class="col-sm-6"><div class="text-muted">Scholarship</div><strong><?= clean($app['scholarship_name']) ?></strong></div>
        <div class="col-sm-6"><div class="text-muted">Student ID</div><strong><?= clean($app['student_id_no']) ?></strong></div>
        <div class="col-sm-6"><div class="text-muted">Course</div><strong><?= clean($app['course']) ?> (<?= clean($app['year_level']) ?>)</strong></div>
        <div class="col-sm-6">
          <div class="text-muted">GWA</div>
          <strong><?= number_format($app['gwa'], 2) ?></strong>
          <?php if ($app['gwa'] <= $app['max_gwa']): ?>
            <span class="text-success">meets requirement (<?= number_format($app['max_gwa'], 2) ?>)</span>
          <?php else: ?>
            <span class="text-danger">above limit (<?= number_format($app['max_gwa'], 2) ?>)</span>
          <?php endif; ?>
        </div>
        <div class="col-sm-6"><div class="text-muted">Email</div><strong><?= clean($app['email']) ?></strong></div>
        <div class="col-sm-6"><div class="text-muted">Phone</div><strong><?= clean($app['phone']) ?></strong></div>
      </div>
      <div class="text-muted small">Reason for applying</div>
      <div class="p-3 bg-light rounded mb-3"><?= nl2br(clean($app['reason'])) ?></div>
      <div class="text-muted small mb-1">Supporting document</div>
      <?php if ($app['document']): ?>
        <a href="../uploads/<?= clean($app['document']) ?>" target="_blank" class="btn btn-sm btn-outline-brand"><i class="bi bi-file-earmark-text"></i> Open document</a>
      <?php else: ?>
        <span class="text-muted">None uploaded.</span>
      <?php endif; ?>
    </div>
  </div>

  <div class="col-lg-5">
    <div class="card p-4 review-box">
      <h6 class="mb-3">Review Decision</h6>
      <?php if ($error != ''): ?>
        <div class="alert alert-danger py-2"><?= clean($error) ?></div>
      <?php endif; ?>
      <form method="post">
        <div class="mb-3">
          <label class="form-label" for="status">Status</label>
          <select id="status" name="status" class="form-select">
            <?php foreach (getStatuses() as $st): ?>
              <option <?= $app['status'] == $st ? 'selected' : '' ?>><?= $st ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label" for="remarks">Message to the applicant</label>
          <textarea id="remarks" name="remarks" rows="4" class="form-control" placeholder="Shown on the tracking page"><?= clean($app['remarks']) ?></textarea>
        </div>
        <button class="btn btn-brand w-100">Save Decision</button>
      </form>
      <hr>
      <a href="application_edit.php?id=<?= $id ?>" class="btn btn-outline-primary w-100"><i class="bi bi-pencil"></i> Edit applicant details</a>
    </div>
  </div>
</div>
<?php include '../includes/admin_footer.php'; ?>
