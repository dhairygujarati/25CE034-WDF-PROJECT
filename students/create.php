<?php
/**
 * Student Management - Create New Student
 * Item 11: MySQL Prepared Statements & Form Validation
 */

$pageTitle = 'Add New Student';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/csrf.php';

require_role('admin');

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

    // Validations
    if (empty($rollNumber)) $errors['roll_number'] = 'Roll number is required.';
    if (empty($firstName))  $errors['first_name'] = 'First name is required.';
    if (empty($lastName))   $errors['last_name'] = 'Last name is required.';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Valid email is required.';
    if (empty($phone))      $errors['phone'] = 'Phone number is required.';
    if (empty($department)) $errors['department'] = 'Department is required.';

    if (empty($errors)) {
        try {
            $pdo = Database::getConnection();

            // Duplicate checks
            $chk = $pdo->prepare("SELECT id FROM students WHERE roll_number = :roll OR email = :email LIMIT 1");
            $chk->execute([':roll' => $rollNumber, ':email' => $email]);
            if ($chk->fetch()) {
                $errors['general'] = 'A student with this Roll Number or Email address already exists.';
            } else {
                $insert = $pdo->prepare("
                    INSERT INTO students (roll_number, first_name, last_name, email, phone, department, semester, gpa, status)
                    VALUES (:roll, :fname, :lname, :email, :phone, :dept, :sem, :gpa, :status)
                ");
                $insert->execute([
                    ':roll'   => $rollNumber,
                    ':fname'  => $firstName,
                    ':lname'  => $lastName,
                    ':email'  => $email,
                    ':phone'  => $phone,
                    ':dept'   => $department,
                    ':sem'    => $semester,
                    ':gpa'    => $gpa,
                    ':status' => $status
                ]);

                $newId = (int)$pdo->lastInsertId();
                log_activity(current_user()['id'], 'CREATE_STUDENT', "Created student #$newId ($rollNumber)");

                set_flash('success', "Student {$firstName} {$lastName} ({$rollNumber}) added successfully!");
                header('Location: ' . base_url('students/index.php'));
                exit;
            }
        } catch (Exception $e) {
            $errors['general'] = 'Database Error: ' . $e->getMessage();
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container" style="max-width: 820px;">
  <div style="margin-bottom: 2rem;">
    <a href="<?= base_url('students/index.php') ?>" style="font-weight: 600; font-size: 0.9rem;">&larr; Back to Student Directory</a>
    <h1 style="margin-top: 0.5rem;">Add New Student</h1>
    <p style="color: var(--text-muted);">Enter student academic details. All fields marked with an asterisk are required.</p>
  </div>

  <?php if (!empty($errors['general'])): ?>
    <div class="alert alert-danger" role="alert">
      <?= e($errors['general']) ?>
    </div>
  <?php endif; ?>

  <div class="card">
    <div class="card-body">
      <form method="POST" action="<?= base_url('students/create.php') ?>" novalidate>
        <?= csrf_field() ?>

        <div class="form-row-2">
          <div class="form-group">
            <label for="roll_number" class="form-label">Roll Number / Student ID <span class="required">*</span></label>
            <input type="text" id="roll_number" name="roll_number" class="form-control <?= isset($errors['roll_number']) ? 'is-invalid' : '' ?>" value="<?= e($_POST['roll_number'] ?? '') ?>" placeholder="e.g. CS-2026-045" required>
            <?php if (isset($errors['roll_number'])): ?><div class="invalid-feedback"><?= e($errors['roll_number']) ?></div><?php endif; ?>
          </div>

          <div class="form-group">
            <label for="email" class="form-label">Email Address <span class="required">*</span></label>
            <input type="email" id="email" name="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" value="<?= e($_POST['email'] ?? '') ?>" placeholder="student@campus.edu" required>
            <?php if (isset($errors['email'])): ?><div class="invalid-feedback"><?= e($errors['email']) ?></div><?php endif; ?>
          </div>
        </div>

        <div class="form-row-2">
          <div class="form-group">
            <label for="first_name" class="form-label">First Name <span class="required">*</span></label>
            <input type="text" id="first_name" name="first_name" class="form-control <?= isset($errors['first_name']) ? 'is-invalid' : '' ?>" value="<?= e($_POST['first_name'] ?? '') ?>" required>
            <?php if (isset($errors['first_name'])): ?><div class="invalid-feedback"><?= e($errors['first_name']) ?></div><?php endif; ?>
          </div>

          <div class="form-group">
            <label for="last_name" class="form-label">Last Name <span class="required">*</span></label>
            <input type="text" id="last_name" name="last_name" class="form-control <?= isset($errors['last_name']) ? 'is-invalid' : '' ?>" value="<?= e($_POST['last_name'] ?? '') ?>" required>
            <?php if (isset($errors['last_name'])): ?><div class="invalid-feedback"><?= e($errors['last_name']) ?></div><?php endif; ?>
          </div>
        </div>

        <div class="form-row-3">
          <div class="form-group">
            <label for="phone" class="form-label">Phone Number <span class="required">*</span></label>
            <input type="tel" id="phone" name="phone" class="form-control <?= isset($errors['phone']) ? 'is-invalid' : '' ?>" value="<?= e($_POST['phone'] ?? '') ?>" placeholder="+1-555-0155" required>
            <?php if (isset($errors['phone'])): ?><div class="invalid-feedback"><?= e($errors['phone']) ?></div><?php endif; ?>
          </div>

          <div class="form-group">
            <label for="department" class="form-label">Department <span class="required">*</span></label>
            <select id="department" name="department" class="form-control" required>
              <option value="Computer Science">Computer Science</option>
              <option value="Information Technology">Information Technology</option>
              <option value="Data Science">Data Science</option>
              <option value="Electronics Engineering">Electronics Engineering</option>
              <option value="Mechanical Engineering">Mechanical Engineering</option>
            </select>
          </div>

          <div class="form-group">
            <label for="semester" class="form-label">Current Semester <span class="required">*</span></label>
            <input type="number" id="semester" name="semester" min="1" max="10" class="form-control" value="<?= e($_POST['semester'] ?? '1') ?>" required>
          </div>
        </div>

        <div class="form-row-2">
          <div class="form-group">
            <label for="gpa" class="form-label">Current GPA (0.00 - 4.00)</label>
            <input type="number" id="gpa" name="gpa" step="0.01" min="0" max="4.00" class="form-control" value="<?= e($_POST['gpa'] ?? '3.50') ?>">
          </div>

          <div class="form-group">
            <label for="status" class="form-label">Enrollment Status <span class="required">*</span></label>
            <select id="status" name="status" class="form-control" required>
              <option value="Active">Active</option>
              <option value="Inactive">Inactive</option>
              <option value="Graduated">Graduated</option>
              <option value="Suspended">Suspended</option>
            </select>
          </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem;">
          <a href="<?= base_url('students/index.php') ?>" class="btn btn-outline">Cancel</a>
          <button type="submit" class="btn btn-primary">&#x2713; Save Student Record</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
