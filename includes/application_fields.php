<?php if (count($errors) > 0): ?>
  <div class="alert alert-danger">
    <strong>Please fix the following:</strong>
    <ul class="mb-0">
      <?php foreach ($errors as $error): ?>
        <li><?= clean($error) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>
<div class="row g-3">
  <div class="col-md-6">
    <label class="form-label" for="full_name">Full Name</label>
    <input type="text" id="full_name" name="full_name" class="form-control" maxlength="100" value="<?= clean($a['full_name']) ?>" required>
  </div>
  <div class="col-md-6">
    <label class="form-label" for="student_id_no">Student ID Number</label>
    <input type="text" id="student_id_no" name="student_id_no" class="form-control" maxlength="30" value="<?= clean($a['student_id_no']) ?>" placeholder="2023-00123" required>
  </div>
  <div class="col-md-6">
    <label class="form-label" for="email">Email Address</label>
    <input type="email" id="email" name="email" class="form-control" maxlength="100" value="<?= clean($a['email']) ?>" required>
  </div>
  <div class="col-md-6">
    <label class="form-label" for="phone">Phone Number</label>
    <input type="text" id="phone" name="phone" class="form-control" maxlength="20" value="<?= clean($a['phone']) ?>" placeholder="09123456789" required>
  </div>
  <div class="col-md-6">
    <label class="form-label" for="course">Course / Program</label>
    <input type="text" id="course" name="course" class="form-control" maxlength="100" value="<?= clean($a['course']) ?>" placeholder="BS Information Technology" required>
  </div>
  <div class="col-md-3">
    <label class="form-label" for="year_level">Year Level</label>
    <select id="year_level" name="year_level" class="form-select" required>
      <option value="">Choose</option>
      <?php foreach (getYearLevels() as $y): ?>
        <option <?= $a['year_level'] == $y ? 'selected' : '' ?>><?= $y ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-md-3">
    <label class="form-label" for="gwa">GWA</label>
    <input type="number" step="0.01" min="1" max="5" id="gwa" name="gwa" class="form-control" value="<?= clean($a['gwa']) ?>" placeholder="1.75" required>
  </div>
  <div class="col-12">
    <label class="form-label" for="reason">Why do you deserve this scholarship?</label>
    <textarea id="reason" name="reason" rows="5" class="form-control" maxlength="1500" required><?= clean($a['reason']) ?></textarea>
    <div class="form-text">At least 30 characters.</div>
  </div>
  <?php if (isset($showUpload) && $showUpload): ?>
  <div class="col-12">
    <label class="form-label" for="document">Supporting Document (optional)</label>
    <input type="file" id="document" name="document" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
    <div class="form-text">Grade report or certificate. PDF, JPG or PNG, up to 2 MB.</div>
  </div>
  <?php endif; ?>
</div>
