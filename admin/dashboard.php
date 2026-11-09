<?php
require_once '../includes/functions.php';
requireAdmin();

$counts = array('Pending' => 0, 'Under Review' => 0, 'Approved' => 0, 'Rejected' => 0);
$result = $conn->query("SELECT status, COUNT(*) AS total FROM applications GROUP BY status");
while ($row = $result->fetch_assoc()) {
    $counts[$row['status']] = $row['total'];
}
$totalApps = array_sum($counts);
$openCount = $conn->query("SELECT COUNT(*) AS total FROM scholarships WHERE status = 'Open' AND deadline >= CURDATE()")->fetch_assoc()['total'];

$sql = "SELECT s.scholarship_name, s.slots,
        (SELECT COUNT(*) FROM applications a WHERE a.scholarship_id = s.scholarship_id) AS total,
        (SELECT COUNT(*) FROM applications a WHERE a.scholarship_id = s.scholarship_id AND a.status = 'Approved') AS approved
        FROM scholarships s ORDER BY s.scholarship_name";
$perScholarship = $conn->query($sql);

$recent = $conn->query("SELECT a.*, s.scholarship_name FROM applications a
                        JOIN scholarships s ON a.scholarship_id = s.scholarship_id
                        ORDER BY a.created_at DESC, a.application_id DESC LIMIT 6");

$pageTitle = 'Dashboard';
include '../includes/admin_header.php';
?>
<div class="row g-3 mb-4">
  <div class="col-6 col-xl-3">
    <div class="card p-3"><div class="d-flex align-items-center gap-3">
      <div class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-files"></i></div>
      <div><div class="text-muted small">Total Applications</div><div class="stat-number"><?= $totalApps ?></div></div>
    </div></div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="card p-3"><div class="d-flex align-items-center gap-3">
      <div class="stat-icon bg-warning bg-opacity-25 text-warning"><i class="bi bi-hourglass-split"></i></div>
      <div><div class="text-muted small">Need Review</div><div class="stat-number"><?= $counts['Pending'] + $counts['Under Review'] ?></div></div>
    </div></div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="card p-3"><div class="d-flex align-items-center gap-3">
      <div class="stat-icon bg-success bg-opacity-10 text-success"><i class="bi bi-check-circle"></i></div>
      <div><div class="text-muted small">Approved</div><div class="stat-number"><?= $counts['Approved'] ?></div></div>
    </div></div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="card p-3"><div class="d-flex align-items-center gap-3">
      <div class="stat-icon bg-info bg-opacity-10 text-info"><i class="bi bi-award"></i></div>
      <div><div class="text-muted small">Open Scholarships</div><div class="stat-number"><?= $openCount ?></div></div>
    </div></div>
  </div>
</div>

<div class="row g-3">
  <div class="col-lg-5">
    <div class="card p-3 h-100">
      <h6 class="mb-3">Slots Filled per Scholarship</h6>
      <?php while ($p = $perScholarship->fetch_assoc()): ?>
        <?php $percent = $p['slots'] > 0 ? min(100, round($p['approved'] / $p['slots'] * 100)) : 0; ?>
        <div class="mb-3">
          <div class="d-flex justify-content-between small">
            <span><?= clean($p['scholarship_name']) ?></span>
            <span class="text-muted"><?= $p['approved'] ?>/<?= $p['slots'] ?> approved &middot; <?= $p['total'] ?> applicants</span>
          </div>
          <div class="progress" style="height: 8px;">
            <div class="progress-bar bg-success" style="width: <?= $percent ?>%"></div>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  </div>
  <div class="col-lg-7">
    <div class="card p-3 h-100">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="mb-0">Recent Applications</h6>
        <a href="applications.php" class="btn btn-sm btn-outline-brand">View all</a>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light"><tr><th>Applicant</th><th>Scholarship</th><th>Status</th></tr></thead>
          <tbody>
            <?php if ($recent->num_rows == 0): ?>
              <tr><td colspan="3" class="text-center text-muted py-4">No applications yet.</td></tr>
            <?php endif; ?>
            <?php while ($r = $recent->fetch_assoc()): ?>
              <tr>
                <td><a href="application_view.php?id=<?= $r['application_id'] ?>" class="fw-semibold text-decoration-none"><?= clean($r['full_name']) ?></a>
                  <div class="small text-muted"><?= clean($r['reference_code']) ?></div></td>
                <td class="small"><?= clean($r['scholarship_name']) ?></td>
                <td><?= statusBadge($r['status']) ?></td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?php include '../includes/admin_footer.php'; ?>
