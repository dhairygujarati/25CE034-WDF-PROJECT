<?php
/**
 * Student Management - Edit Existing Student
 * Item 11: Prepared UPDATE Statement & Validation
 */

$pageTitle = 'Edit Student Record';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/csrf.php';

require_role('admin');

$pdo = Database::getConnection();
$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    set_flash('danger', 'Invalid student ID provided.');
    header('Location: ' . base_url('students/index.php'));
    exit;
}

// Fetch existing record
$stmt = $pdo->prepare("SELECT * FROM students WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $id]);
$student = $stmt->fetch();

if (!$student) {
    set_flash('danger', 'Student record not found.');
    header('Location: ' . base_url('students/index.php'));
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();

    $rollNumber = trim($_POST['roll_number'] ?? '');
    $firstName  = trim($_POST['first_name'] ?? '');
    $lastName   = trim($_POST['last_name'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $phone      = trim($_POST['phone'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $semester   = (int)($_POST['semester'] ?? 1);
    $gpa        = (float)($_POST['gpa'] ?? 0.00);
    $status     = trim($_POST['status'] ?? 'Active');

    if (empty($rollNumber)) $errors['roll_number'] = 'Roll number is required.';
    if (empty($firstName))  $errors['first_name'] = 'First name is required.';
    if (empty($lastName))   $errors['last_name'] = 'Last name is required.';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Valid email is required.';
    if (empty($phone))      $errors['phone'] = 'Phone number is required.';
    if (empty($department)) $errors['department'] = 'Department is required.';

    if (empty($errors)) {
        try {
            // Check duplicates excluding this ID
            $chk = $pdo->prepare("SELECT id FROM students WHERE (roll_number = :roll OR email = :email) AND id != :id LIMIT 1");
            $chk->execute([':roll' => $rollNumber, ':email' => $email, ':id' => $id]);
            if ($chk->fetch()) {
                $errors['general'] = 'Another student already has this Roll Number or Email address.';
            } else {
                $update = $pdo->prepare("
                    UPDATE students SET
                        roll_number = :roll,
                        first_name  = :fname,
                        last_name   = :lname,
                        email       = :email,
                        phone       = :phone,
                        department  = :dept,
                        semester    = :sem,
                        gpa         = :gpa,
                        status      = :status
                    WHERE id = :id
                ");
                $update->execute([
                    ':roll'   => $rollNumber,
                    ':fname'  => $firstName,
                    ':lname'  => $lastName,
                    ':email'  => $email,
                    ':phone'  => $phone,
                    ':dept'   => $department,
                    ':sem'    => $semester,
                    ':gpa'    => $gpa,
                    ':status' => $status,
                    ':id'     => $id
                ]);

                log_activity(current_user()['id'], 'UPDATE_STUDENT', "Updated student #$id ($rollNumber)");
                set_flash('success', "Student record #{$id} ({$rollNumber}) updated successfully!");
                header('Location: ' . base_url('students/index.php'));
                exit;
            }
        } catch (Exception $e) {
            $errors['general'] = 'Update Error: ' . $e->getMessage();
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container" style="max-width: 820px;">
  <div style="margin-bottom: 2rem;">
    <a href="<?= base_url('students/index.php') ?>" style="font-weight: 600; font-size: 0.9rem;">&larr; Back to Student Directory</a>
    <h1 style="margin-top: 0.5rem;">Edit Student: <?= e($student['roll_number']) ?></h1>
    <p style="color: var(--text-muted);">Update existing student academic profile and enrollment status.</p>
  </div>

  <?php if (!empty($errors['general'])): ?>
    <div class="alert alert-danger" role="alert">
      <?= e($errors['general']) ?>
    </div>
  <?php endif; ?>

  <div class="card">
    <div class="card-body">
      <form method="POST" action="<?= base_url('students/edit.php?id=' . $id) ?>" novalidate>
        <?= csrf_field() ?>

        <div class="form-row-2">
          <div class="form-group">
            <label for="roll_number" class="form-label">Roll Number / Student ID <span class="required">*</span></label>
            <input type="text" id="roll_number" name="roll_number" class="form-control" value="<?= e($_POST['roll_number'] ?? $student['roll_number']) ?>" required>
          </div>

          <div class="form-group">
            <label for="email" class="form-label">Email Address <span class="required">*</span></label>
            <input type="email" id="email" name="email" class="form-control" value="<?= e($_POST['email'] ?? $student['email']) ?>" required>
          </div>
        </div>

        <div class="form-row-2">
          <div class="form-group">
            <label for="first_name" class="form-label">First Name <span class="required">*</span></label>
            <input type="text" id="first_name" name="first_name" class="form-control" value="<?= e($_POST['first_name'] ?? $student['first_name']) ?>" required>
          </div>

          <div class="form-group">
            <label for="last_name" class="form-label">Last Name <span class="required">*</span></label>
            <input type="text" id="last_name" name="last_name" class="form-control" value="<?= e($_POST['last_name'] ?? $student['last_name']) ?>" required>
          </div>
        </div>

        <div class="form-row-3">
          <div class="form-group">
            <label for="phone" class="form-label">Phone Number <span class="required">*</span></label>
            <input type="tel" id="phone" name="phone" class="form-control" value="<?= e($_POST['phone'] ?? $student['phone']) ?>" required>
          </div>

          <div class="form-group">
            <label for="department" class="form-label">Department <span class="required">*</span></label>
            <?php $currDept = $_POST['department'] ?? $student['department']; ?>
            <select id="department" name="department" class="form-control" required>
              <option value="Computer Science" <?= ($currDept === 'Computer Science') ? 'selected' : '' ?>>Computer Science</option>
              <option value="Information Technology" <?= ($currDept === 'Information Technology') ? 'selected' : '' ?>>Information Technology</option>
              <option value="Data Science" <?= ($currDept === 'Data Science') ? 'selected' : '' ?>>Data Science</option>
              <option value="Electronics Engineering" <?= ($currDept === 'Electronics Engineering') ? 'selected' : '' ?>>Electronics Engineering</option>
              <option value="Mechanical Engineering" <?= ($currDept === 'Mechanical Engineering') ? 'selected' : '' ?>>Mechanical Engineering</option>
            </select>
          </div>

          <div class="form-group">
            <label for="semester" class="form-label">Current Semester <span class="required">*</span></label>
            <input type="number" id="semester" name="semester" min="1" max="10" class="form-control" value="<?= e($_POST['semester'] ?? $student['semester']) ?>" required>
          </div>
        </div>

        <div class="form-row-2">
          <div class="form-group">
            <label for="gpa" class="form-label">Current GPA</label>
            <input type="number" id="gpa" name="gpa" step="0.01" min="0" max="4.00" class="form-control" value="<?= e($_POST['gpa'] ?? $student['gpa']) ?>">
          </div>

          <div class="form-group">
            <label for="status" class="form-label">Enrollment Status <span class="required">*</span></label>
            <?php $currStatus = $_POST['status'] ?? $student['status']; ?>
            <select id="status" name="status" class="form-control" required>
              <option value="Active" <?= ($currStatus === 'Active') ? 'selected' : '' ?>>Active</option>
              <option value="Inactive" <?= ($currStatus === 'Inactive') ? 'selected' : '' ?>>Inactive</option>
              <option value="Graduated" <?= ($currStatus === 'Graduated') ? 'selected' : '' ?>>Graduated</option>
              <option value="Suspended" <?= ($currStatus === 'Suspended') ? 'selected' : '' ?>>Suspended</option>
            </select>
          </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem;">
          <a href="<?= base_url('students/index.php') ?>" class="btn btn-outline">Cancel</a>
          <button type="submit" class="btn btn-primary">&#x2713; Update Student Record</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
