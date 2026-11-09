<?php
require_once 'includes/functions.php';

$ref = '';
$email = '';
$app = null;
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ref = strtoupper(trim($_POST['reference_code']));
    $email = trim($_POST['email']);

    $sql = "SELECT a.*, s.scholarship_name, s.benefit_amount FROM applications a
            JOIN scholarships s ON a.scholarship_id = s.scholarship_id
            WHERE a.reference_code = ? AND a.email = ?";
    $app = run($conn, $sql, 'ss', array($ref, $email))->get_result()->fetch_assoc();

    if (!$app) {
        $message = 'No application matches that reference code and email.';
    }
}

$steps = array('Pending', 'Under Review', 'Approved');
$pageTitle = 'Track Application';
include 'includes/header.php';
?>
<div class="row justify-content-center">
  <div class="col-lg-6">
    <div class="card p-4 mb-4">
      <h4 class="mb-1"><i class="bi bi-search text-success"></i> Track Your Application</h4>
      <p class="text-muted">Enter your reference code and the email you used to apply.</p>
      <?php if ($message != ''): ?>
        <div class="alert alert-danger"><?= clean($message) ?></div>
      <?php endif; ?>
      <form method="post">
        <div class="mb-3">
          <label class="form-label" for="reference_code">Reference Code</label>
          <input type="text" id="reference_code" name="reference_code" class="form-control" placeholder="SCH123ABC" value="<?= clean($ref) ?>" required>
        </div>
        <div class="mb-3">
          <label class="form-label" for="email">Email</label>
          <input type="email" id="email" name="email" class="form-control" value="<?= clean($email) ?>" required>
        </div>
        <button class="btn btn-brand">Check Status</button>
      </form>
    </div>

    <?php if ($app): ?>
      <div class="card p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="mb-0"><?= clean($app['reference_code']) ?></h5>
          <?= statusBadge($app['status']) ?>
        </div>

        <?php if ($app['status'] == 'Rejected'): ?>
          <div class="alert alert-danger py-2">Your application was not approved.</div>
        <?php else: ?>
          <div class="row text-center mb-3 g-2">
            <?php foreach ($steps as $i => $step): ?>
              <?php $done = array_search($app['status'], $steps) >= $i; ?>
              <div class="col-4">
                <div class="py-2 rounded <?= $done ? 'bg-success text-white' : 'bg-light text-muted' ?> small fw-semibold"><?= $step ?></div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <ul class="list-group list-group-flush mb-3">
          <li class="list-group-item d-flex justify-content-between"><span>Applicant</span><strong><?= clean($app['full_name']) ?></strong></li>
          <li class="list-group-item d-flex justify-content-between"><span>Scholarship</span><strong><?= clean($app['scholarship_name']) ?></strong></li>
          <li class="list-group-item d-flex justify-content-between"><span>Benefit</span><strong><?= peso($app['benefit_amount']) ?></strong></li>
          <li class="list-group-item d-flex justify-content-between"><span>Submitted</span><strong><?= formatDate($app['created_at']) ?></strong></li>
        </ul>

        <?php if ($app['remarks'] != ''): ?>
          <div class="review-box p-3">
            <div class="small text-muted mb-1">Message from the scholarship office</div>
            <?= nl2br(clean($app['remarks'])) ?>
          </div>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
