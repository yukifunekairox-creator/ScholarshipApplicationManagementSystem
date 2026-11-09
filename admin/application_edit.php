<?php
require_once '../includes/functions.php';
requireAdmin();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$app = run($conn, "SELECT * FROM applications WHERE application_id = ?", 'i', array($id))->get_result()->fetch_assoc();
if (!$app) {
    setFlash('danger', 'Application not found.');
    header('Location: applications.php');
    exit;
}

$scholarship = run($conn, "SELECT * FROM scholarships WHERE scholarship_id = ?", 'i', array($app['scholarship_id']))->get_result()->fetch_assoc();
$a = $app;
$errors = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $a = getApplicationData();
    $errors = validateApplication($conn, $a, $scholarship, $id, true);

    if (count($errors) == 0) {
        $sql = "UPDATE applications SET student_id_no = ?, full_name = ?, email = ?, phone = ?, course = ?,
                year_level = ?, gwa = ?, reason = ? WHERE application_id = ?";
        run($conn, $sql, 'ssssssdsi', array($a['student_id_no'], $a['full_name'], $a['email'], $a['phone'],
            $a['course'], $a['year_level'], $a['gwa'], $a['reason'], $id));
        setFlash('success', 'Application updated.');
        header('Location: application_view.php?id=' . $id);
        exit;
    }
}

$pageTitle = 'Edit Application';
include '../includes/admin_header.php';
?>
<div class="card p-4">
  <h5 class="mb-1">Edit Application</h5>
  <p class="text-muted small"><?= clean($app['reference_code']) ?> &middot; <?= clean($scholarship['scholarship_name']) ?></p>
  <form method="post">
    <?php include '../includes/application_fields.php'; ?>
    <div class="mt-4">
      <button class="btn btn-brand">Update Application</button>
      <a href="application_view.php?id=<?= $id ?>" class="btn btn-outline-secondary">Cancel</a>
    </div>
  </form>
</div>
<?php include '../includes/admin_footer.php'; ?>
