<?php
require_once 'includes/functions.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$s = run($conn, "SELECT * FROM scholarships WHERE scholarship_id = ?", 'i', array($id))->get_result()->fetch_assoc();

if (!$s) {
    setFlash('danger', 'Scholarship not found.');
    header('Location: index.php');
    exit;
}

$canApply = isOpenForApplying($s) && approvedCount($conn, $id) < $s['slots'];

$a = array('student_id_no' => '', 'full_name' => '', 'email' => '', 'phone' => '', 'course' => '', 'year_level' => '', 'gwa' => '', 'reason' => '');
$errors = array();

if ($canApply && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $a = getApplicationData();
    $errors = validateApplication($conn, $a, $s);

    if (count($errors) == 0) {
        $document = saveDocument($errors);
    }

    if (count($errors) == 0) {
        $code = makeReference($conn);
        $sql = "INSERT INTO applications (reference_code, scholarship_id, student_id_no, full_name, email, phone, course, year_level, gwa, reason, document)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        run($conn, $sql, 'sissssssdss', array($code, $id, $a['student_id_no'], $a['full_name'], $a['email'],
            $a['phone'], $a['course'], $a['year_level'], $a['gwa'], $a['reason'], $document));
        header('Location: success.php?ref=' . $code);
        exit;
    }
}

$showUpload = true;
$pageTitle = 'Apply';
include 'includes/header.php';
?>
<div class="row g-4">
  <div class="col-lg-4 order-lg-2">
    <div class="card p-4">
      <h5><?= clean($s['scholarship_name']) ?></h5>
      <div class="text-muted small mb-3"><?= clean($s['sponsor']) ?></div>
      <p class="small"><?= clean($s['description']) ?></p>
      <ul class="list-unstyled small mb-0">
        <li class="mb-2"><i class="bi bi-cash-coin text-success"></i> Benefit: <strong><?= peso($s['benefit_amount']) ?></strong></li>
        <li class="mb-2"><i class="bi bi-mortarboard text-success"></i> GWA: <strong><?= number_format($s['max_gwa'], 2) ?> or better</strong></li>
        <li class="mb-2"><i class="bi bi-people text-success"></i> Slots left: <strong><?= max(0, $s['slots'] - approvedCount($conn, $id)) ?></strong></li>
        <li><i class="bi bi-calendar-event text-success"></i> Deadline: <strong><?= formatDate($s['deadline']) ?></strong></li>
      </ul>
    </div>
  </div>
  <div class="col-lg-8 order-lg-1">
    <div class="card p-4">
      <h4 class="mb-3">Scholarship Application</h4>
      <?php if (!$canApply): ?>
        <div class="alert alert-warning mb-0">Applications for this scholarship are closed or there are no slots left. <a href="index.php">Back to scholarships</a></div>
      <?php else: ?>
        <form method="post" enctype="multipart/form-data">
          <?php include 'includes/application_fields.php'; ?>
          <div class="mt-4 d-grid d-md-block">
            <button type="submit" class="btn btn-brand btn-lg px-5">Submit Application</button>
            <a href="index.php" class="btn btn-outline-secondary btn-lg">Cancel</a>
          </div>
        </form>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
