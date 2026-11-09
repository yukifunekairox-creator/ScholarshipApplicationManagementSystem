<?php
require_once '../includes/functions.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['action'] == 'delete') {
    $id = (int) $_POST['id'];
    $old = run($conn, "SELECT document FROM applications WHERE application_id = ?", 'i', array($id))->get_result()->fetch_assoc();
    if ($old) {
        deleteDocument($old['document']);
    }
    run($conn, "DELETE FROM applications WHERE application_id = ?", 'i', array($id));
    setFlash('success', 'Application deleted.');
    $back = 'applications.php';
    if (!empty($_POST['return'])) {
        $back .= '?' . $_POST['return'];
    }
    header('Location: ' . $back);
    exit;
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
$scholarship = isset($_GET['scholarship']) ? (int) $_GET['scholarship'] : 0;

$sql = "SELECT a.*, s.scholarship_name FROM applications a
        JOIN scholarships s ON a.scholarship_id = s.scholarship_id WHERE 1=1";
$types = '';
$params = array();

if ($search != '') {
    $sql .= " AND (a.full_name LIKE ? OR a.student_id_no LIKE ? OR a.reference_code LIKE ? OR a.email LIKE ?)";
    $like = '%' . $search . '%';
    $types .= 'ssss';
    array_push($params, $like, $like, $like, $like);
}
if ($status != '') {
    $sql .= " AND a.status = ?";
    $types .= 's';
    $params[] = $status;
}
if ($scholarship > 0) {
    $sql .= " AND a.scholarship_id = ?";
    $types .= 'i';
    $params[] = $scholarship;
}
$sql .= " ORDER BY a.created_at DESC, a.application_id DESC";

$result = run($conn, $sql, $types, $params)->get_result();
$scholarshipList = $conn->query("SELECT scholarship_id, scholarship_name FROM scholarships ORDER BY scholarship_name");
$returnQuery = $_SERVER['QUERY_STRING'];

$pageTitle = 'Applications';
include '../includes/admin_header.php';
?>
<div class="card p-3">
  <form class="row g-2 mb-3" method="get">
    <div class="col-md-4">
      <input type="text" name="search" class="form-control" placeholder="Search name, student ID, email or reference" value="<?= clean($search) ?>">
    </div>
    <div class="col-md-3">
      <select name="scholarship" class="form-select">
        <option value="0">All scholarships</option>
        <?php while ($sl = $scholarshipList->fetch_assoc()): ?>
          <option value="<?= $sl['scholarship_id'] ?>" <?= $scholarship == $sl['scholarship_id'] ? 'selected' : '' ?>><?= clean($sl['scholarship_name']) ?></option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="col-md-2">
      <select name="status" class="form-select">
        <option value="">All statuses</option>
        <?php foreach (getStatuses() as $st): ?>
          <option <?= $status == $st ? 'selected' : '' ?>><?= $st ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-3 d-flex gap-2">
      <button class="btn btn-brand flex-fill">Filter</button>
      <a href="applications.php" class="btn btn-outline-secondary">Reset</a>
    </div>
  </form>

  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead class="table-light">
        <tr><th>Reference</th><th>Applicant</th><th>Scholarship</th><th>GWA</th><th>Submitted</th><th>Status</th><th class="text-end">Actions</th></tr>
      </thead>
      <tbody>
        <?php if ($result->num_rows == 0): ?>
          <tr><td colspan="7" class="text-center text-muted py-4">No applications found.</td></tr>
        <?php endif; ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td class="small fw-semibold"><?= clean($row['reference_code']) ?></td>
            <td>
              <div class="fw-semibold"><?= clean($row['full_name']) ?></div>
              <div class="small text-muted"><?= clean($row['student_id_no']) ?> &middot; <?= clean($row['course']) ?></div>
            </td>
            <td class="small"><?= clean($row['scholarship_name']) ?></td>
            <td><?= number_format($row['gwa'], 2) ?></td>
            <td class="small"><?= formatDate($row['created_at']) ?></td>
            <td><?= statusBadge($row['status']) ?></td>
            <td class="text-end text-nowrap">
              <a href="application_view.php?id=<?= $row['application_id'] ?>" class="btn btn-sm btn-brand" title="Review"><i class="bi bi-eye"></i></a>
              <a href="application_edit.php?id=<?= $row['application_id'] ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
              <form method="post" class="d-inline" onsubmit="return confirm('Delete this application permanently?');">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= $row['application_id'] ?>">
                <input type="hidden" name="return" value="<?= clean($returnQuery) ?>">
                <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
              </form>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include '../includes/admin_footer.php'; ?>
