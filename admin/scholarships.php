<?php
require_once '../includes/functions.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['action'] == 'delete') {
    $id = (int) $_POST['id'];
    $used = run($conn, "SELECT COUNT(*) AS total FROM applications WHERE scholarship_id = ?", 'i', array($id))->get_result()->fetch_assoc()['total'];

    if ($used > 0) {
        setFlash('danger', 'This scholarship has ' . $used . ' application(s) and cannot be deleted. Close it instead.');
    } else {
        run($conn, "DELETE FROM scholarships WHERE scholarship_id = ?", 'i', array($id));
        setFlash('success', 'Scholarship deleted.');
    }
    header('Location: scholarships.php');
    exit;
}

$list = $conn->query("SELECT s.*,
        (SELECT COUNT(*) FROM applications a WHERE a.scholarship_id = s.scholarship_id) AS total,
        (SELECT COUNT(*) FROM applications a WHERE a.scholarship_id = s.scholarship_id AND a.status = 'Approved') AS approved
        FROM scholarships s ORDER BY s.deadline DESC");

$pageTitle = 'Scholarships';
include '../includes/admin_header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <div class="text-muted">Manage the scholarships students can apply for.</div>
  <a href="scholarship_form.php" class="btn btn-brand"><i class="bi bi-plus-lg"></i> Add Scholarship</a>
</div>
<div class="card p-3">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr><th>Scholarship</th><th>Benefit</th><th>Slots</th><th>GWA</th><th>Deadline</th><th>Applicants</th><th>Status</th><th class="text-end">Actions</th></tr>
      </thead>
      <tbody>
        <?php if ($list->num_rows == 0): ?>
          <tr><td colspan="8" class="text-center text-muted py-4">No scholarships yet.</td></tr>
        <?php endif; ?>
        <?php while ($s = $list->fetch_assoc()): ?>
          <tr>
            <td><div class="fw-semibold"><?= clean($s['scholarship_name']) ?></div><div class="small text-muted"><?= clean($s['sponsor']) ?></div></td>
            <td><?= peso($s['benefit_amount']) ?></td>
            <td><?= $s['approved'] ?>/<?= $s['slots'] ?></td>
            <td><?= number_format($s['max_gwa'], 2) ?></td>
            <td class="small"><?= formatDate($s['deadline']) ?></td>
            <td><?= $s['total'] ?></td>
            <td><?= statusBadge($s['status']) ?></td>
            <td class="text-end text-nowrap">
              <a href="scholarship_form.php?id=<?= $s['scholarship_id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
              <form method="post" class="d-inline" onsubmit="return confirm('Delete this scholarship?');">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= $s['scholarship_id'] ?>">
                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
              </form>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include '../includes/admin_footer.php'; ?>
