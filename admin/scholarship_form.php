<?php
require_once '../includes/functions.php';
requireAdmin();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$s = array('scholarship_name' => '', 'sponsor' => '', 'description' => '', 'benefit_amount' => '',
           'slots' => 5, 'max_gwa' => '2.00', 'deadline' => '', 'status' => 'Open');

if ($id > 0) {
    $found = run($conn, "SELECT * FROM scholarships WHERE scholarship_id = ?", 'i', array($id))->get_result()->fetch_assoc();
    if (!$found) {
        setFlash('danger', 'Scholarship not found.');
        header('Location: scholarships.php');
        exit;
    }
    $s = $found;
}

$errors = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $s['scholarship_name'] = trim($_POST['scholarship_name']);
    $s['sponsor'] = trim($_POST['sponsor']);
    $s['description'] = trim($_POST['description']);
    $s['benefit_amount'] = $_POST['benefit_amount'];
    $s['slots'] = (int) $_POST['slots'];
    $s['max_gwa'] = $_POST['max_gwa'];
    $s['deadline'] = $_POST['deadline'];
    $s['status'] = $_POST['status'];

    if ($s['scholarship_name'] == '') {
        $errors[] = 'Scholarship name is required.';
    }
    if ($s['sponsor'] == '') {
        $errors[] = 'Sponsor is required.';
    }
    if (!is_numeric($s['benefit_amount']) || $s['benefit_amount'] < 0) {
        $errors[] = 'Please enter a valid benefit amount.';
    }
    if ($s['slots'] < 1) {
        $errors[] = 'There must be at least 1 slot.';
    }
    if (!is_numeric($s['max_gwa']) || $s['max_gwa'] < 1 || $s['max_gwa'] > 5) {
        $errors[] = 'The GWA limit must be from 1.00 to 5.00.';
    }
    if ($s['deadline'] == '') {
        $errors[] = 'Deadline is required.';
    }
    if (!in_array($s['status'], array('Open', 'Closed'))) {
        $s['status'] = 'Open';
    }

    if (count($errors) == 0) {
        if ($id > 0) {
            $sql = "UPDATE scholarships SET scholarship_name = ?, sponsor = ?, description = ?, benefit_amount = ?,
                    slots = ?, max_gwa = ?, deadline = ?, status = ? WHERE scholarship_id = ?";
            run($conn, $sql, 'sssdidssi', array($s['scholarship_name'], $s['sponsor'], $s['description'],
                $s['benefit_amount'], $s['slots'], $s['max_gwa'], $s['deadline'], $s['status'], $id));
            setFlash('success', 'Scholarship updated.');
        } else {
            $sql = "INSERT INTO scholarships (scholarship_name, sponsor, description, benefit_amount, slots, max_gwa, deadline, status)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            run($conn, $sql, 'sssdidss', array($s['scholarship_name'], $s['sponsor'], $s['description'],
                $s['benefit_amount'], $s['slots'], $s['max_gwa'], $s['deadline'], $s['status']));
            setFlash('success', 'Scholarship added.');
        }
        header('Location: scholarships.php');
        exit;
    }
}

$pageTitle = $id > 0 ? 'Edit Scholarship' : 'Add Scholarship';
include '../includes/admin_header.php';
?>
<div class="row justify-content-center">
  <div class="col-lg-8">
    <div class="card p-4">
      <?php if (count($errors) > 0): ?>
        <div class="alert alert-danger">
          <ul class="mb-0">
            <?php foreach ($errors as $error): ?><li><?= clean($error) ?></li><?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>
      <form method="post">
        <div class="row g-3">
          <div class="col-md-7">
            <label class="form-label" for="scholarship_name">Scholarship Name</label>
            <input type="text" id="scholarship_name" name="scholarship_name" class="form-control" maxlength="120" value="<?= clean($s['scholarship_name']) ?>" required>
          </div>
          <div class="col-md-5">
            <label class="form-label" for="sponsor">Sponsor</label>
            <input type="text" id="sponsor" name="sponsor" class="form-control" maxlength="120" value="<?= clean($s['sponsor']) ?>" required>
          </div>
          <div class="col-12">
            <label class="form-label" for="description">Description</label>
            <textarea id="description" name="description" rows="3" class="form-control"><?= clean($s['description']) ?></textarea>
          </div>
          <div class="col-md-3">
            <label class="form-label" for="benefit_amount">Benefit (&#8369;)</label>
            <input type="number" step="0.01" min="0" id="benefit_amount" name="benefit_amount" class="form-control" value="<?= clean($s['benefit_amount']) ?>" required>
          </div>
          <div class="col-md-3">
            <label class="form-label" for="slots">Slots</label>
            <input type="number" min="1" id="slots" name="slots" class="form-control" value="<?= clean($s['slots']) ?>" required>
          </div>
          <div class="col-md-3">
            <label class="form-label" for="max_gwa">Required GWA</label>
            <input type="number" step="0.01" min="1" max="5" id="max_gwa" name="max_gwa" class="form-control" value="<?= clean($s['max_gwa']) ?>" required>
          </div>
          <div class="col-md-3">
            <label class="form-label" for="deadline">Deadline</label>
            <input type="date" id="deadline" name="deadline" class="form-control" value="<?= clean($s['deadline']) ?>" required>
          </div>
          <div class="col-md-4">
            <label class="form-label" for="status">Status</label>
            <select id="status" name="status" class="form-select">
              <option <?= $s['status'] == 'Open' ? 'selected' : '' ?>>Open</option>
              <option <?= $s['status'] == 'Closed' ? 'selected' : '' ?>>Closed</option>
            </select>
          </div>
        </div>
        <div class="mt-4">
          <button class="btn btn-brand">Save Scholarship</button>
          <a href="scholarships.php" class="btn btn-outline-secondary">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>
<?php include '../includes/admin_footer.php'; ?>
