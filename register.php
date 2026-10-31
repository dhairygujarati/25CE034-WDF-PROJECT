<?php
/**
 * Campus Portal - Secure Student Registration
 * Items 5 & 9: Frontend & Server-Side Validation, Duplicate Email Check, BCRYPT Password Hashing
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/csrf.php';

// If already logged in, redirect to dashboard
if (is_logged_in()) {
    header('Location: ' . base_url('dashboard.php'));
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();

    $fullName   = trim($_POST['full_name'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $rollNumber = trim($_POST['roll_number'] ?? '');
    $phone      = trim($_POST['phone'] ?? '');
    $department = trim($_POST['department'] ?? 'Computer Science');
    $password   = $_POST['password'] ?? '';
    $confirmPass= $_POST['confirm_password'] ?? '';
    $terms      = isset($_POST['terms_agree']);

    // 1. Server-Side Validations
    if (empty($fullName) || mb_strlen($fullName) < 3) {
        $errors['full_name'] = 'Full name must be at least 3 characters.';
    } elseif (!preg_match("/^[a-zA-Z\s.'-]+$/", $fullName)) {
        $errors['full_name'] = 'Full name contains invalid characters.';
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'A valid email address is required.';
    }

    if (empty($rollNumber)) {
        $errors['roll_number'] = 'Student Roll Number / ID is required.';
    } elseif (!preg_match('/^[A-Z0-9-]{4,20}$/i', $rollNumber)) {
        $errors['roll_number'] = 'Roll number must be alphanumeric (e.g. CS-2024-001).';
    }

    if (empty($phone)) {
        $errors['phone'] = 'Contact phone number is required.';
    }

    // Password requirements: min 8, uppercase, lowercase, number, symbol
    if (strlen($password) < 8) {
        $errors['password'] = 'Password must be at least 8 characters long.';
    } elseif (!preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/\d/', $password)) {
        $errors['password'] = 'Password must contain uppercase letters, lowercase letters, and at least one number.';
    }

    if ($password !== $confirmPass) {
        $errors['confirm_password'] = 'Password confirmation does not match.';
    }

    if (!$terms) {
        $errors['terms'] = 'You must accept the academic code of conduct.';
    }

    // 2. Duplicate Checks (Item 9: Duplicate Email Check)
    if (empty($errors)) {
        try {
            $pdo = Database::getConnection();

            // Duplicate Email Check
            $emailStmt = $pdo->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
            $emailStmt->execute([':email' => $email]);
            if ($emailStmt->fetch()) {
                $errors['email'] = 'This email address is already registered in the portal.';
            }

            // Duplicate Roll Number Check
            $rollStmt = $pdo->prepare("SELECT id FROM students WHERE roll_number = :roll LIMIT 1");
            $rollStmt->execute([':roll' => $rollNumber]);
            if ($rollStmt->fetch()) {
                $errors['roll_number'] = 'This Student Roll Number is already registered.';
            }

            // If completely unique and valid, proceed with transactional insertion
            if (empty($errors)) {
                $pdo->beginTransaction();

                // Item 9: Password Hashing with BCRYPT (Cost factor 12)
                $passwordHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

                // Insert into users table
                $userInsert = $pdo->prepare("
                    INSERT INTO users (full_name, email, password_hash, role)
                    VALUES (:name, :email, :pass, 'student')
                ");
                $userInsert->execute([
                    ':name'  => $fullName,
                    ':email' => $email,
                    ':pass'  => $passwordHash
                ]);

                $newUserId = (int)$pdo->lastInsertId();

                // Split name into first and last name for student record
                $nameParts = explode(' ', $fullName, 2);
                $firstName = $nameParts[0];
                $lastName  = $nameParts[1] ?? 'Student';

                // Insert into students table
                $studentInsert = $pdo->prepare("
                    INSERT INTO students (roll_number, first_name, last_name, email, phone, department, semester, gpa, status)
                    VALUES (:roll, :fname, :lname, :email, :phone, :dept, 1, 0.00, 'Active')
                ");
                $studentInsert->execute([
                    ':roll'  => $rollNumber,
                    ':fname' => $firstName,
                    ':lname' => $lastName,
                    ':email' => $email,
                    ':phone' => $phone,
                    ':dept'  => $department
                ]);

                // Commit transaction
                $pdo->commit();

                // Audit logging
                log_activity($newUserId, 'USER_REGISTERED', "New student registered: $email ($rollNumber)");

                set_flash('success', 'Your student account has been created successfully! Please sign in below.');
                header('Location: ' . base_url('login.php?registered=1'));
                exit;
            }
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $errors['general'] = 'Registration system error: ' . $e->getMessage();
        }
    }
}

$pageTitle = 'Student Registration';
$extraScript = 'js/validation.js';
require_once __DIR__ . '/includes/header.php';
?>

<div class="auth-page-wrapper">
  <div class="auth-card auth-card-wide">
    <div class="auth-header">
      <span class="badge badge-primary">Student Enrollment</span>
      <h1>Student Registration</h1>
      <p style="color: var(--text-muted); font-size: 0.95rem;">
        Enter your academic credentials and details to register for the campus portal.
      </p>
    </div>

    <?php if (isset($errors['general'])): ?>
      <div class="alert alert-danger" role="alert">
        <?= e($errors['general']) ?>
      </div>
    <?php endif; ?>

    <form id="registration-form" method="POST" action="<?= base_url('register.php') ?>" novalidate>
      <?= csrf_field() ?>

      <div class="form-row-2">
        <!-- Full Name -->
        <div class="form-group">
          <label for="full_name" class="form-label">Full Name <span class="required">*</span></label>
          <input 
            type="text" 
            id="full_name" 
            name="full_name" 
            class="form-control <?= isset($errors['full_name']) ? 'is-invalid' : '' ?>" 
            value="<?= e($_POST['full_name'] ?? '') ?>" 
            placeholder="e.g. Maya Lin"
            required
            aria-required="true"
          >
          <div class="invalid-feedback"><?= e($errors['full_name'] ?? 'Please provide a valid full name.') ?></div>
        </div>

        <!-- Email -->
        <div class="form-group">
          <label for="email" class="form-label">Email Address <span class="required">*</span></label>
          <input 
            type="email" 
            id="email" 
            name="email" 
            class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
            value="<?= e($_POST['email'] ?? '') ?>" 
            placeholder="e.g. student@campus.edu"
            required
            aria-required="true"
          >
          <div class="invalid-feedback"><?= e($errors['email'] ?? 'Please provide a valid email address.') ?></div>
        </div>
      </div>

      <div class="form-row-3">
        <!-- Roll Number / Student ID -->
        <div class="form-group">
          <label for="roll_number" class="form-label">Roll Number / ID <span class="required">*</span></label>
          <input 
            type="text" 
            id="roll_number" 
            name="roll_number" 
            class="form-control <?= isset($errors['roll_number']) ? 'is-invalid' : '' ?>" 
            value="<?= e($_POST['roll_number'] ?? '') ?>" 
            placeholder="CS-2026-101"
            required
          >
          <div class="invalid-feedback"><?= e($errors['roll_number'] ?? 'Valid student ID is required.') ?></div>
        </div>

        <!-- Phone -->
        <div class="form-group">
          <label for="phone" class="form-label">Phone Number <span class="required">*</span></label>
          <input 
            type="tel" 
            id="phone" 
            name="phone" 
            class="form-control <?= isset($errors['phone']) ? 'is-invalid' : '' ?>" 
            value="<?= e($_POST['phone'] ?? '') ?>" 
            placeholder="+1-555-0199"
            required
          >
          <div class="invalid-feedback"><?= e($errors['phone'] ?? 'Valid phone number is required.') ?></div>
        </div>

        <!-- Department -->
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
      </div>

      <div class="form-row-2">
        <!-- Password with Strength Bar -->
        <div class="form-group">
          <label for="password" class="form-label">Password <span class="required">*</span></label>
          <input 
            type="password" 
            id="password" 
            name="password" 
            class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" 
            placeholder="Min 8 chars (A-Z, a-z, 0-9, symbol)"
            required
          >
          <div class="strength-meter" aria-hidden="true">
            <div id="strength-bar" class="strength-bar"></div>
          </div>
          <div id="strength-text" class="strength-text"></div>
          <div class="invalid-feedback"><?= e($errors['password'] ?? 'Password must meet complexity requirements.') ?></div>
        </div>

        <!-- Confirm Password -->
        <div class="form-group">
          <label for="confirm_password" class="form-label">Confirm Password <span class="required">*</span></label>
          <input 
            type="password" 
            id="confirm_password" 
            name="confirm_password" 
            class="form-control <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>" 
            placeholder="Repeat your password"
            required
          >
          <div class="invalid-feedback"><?= e($errors['confirm_password'] ?? 'Passwords do not match.') ?></div>
        </div>
      </div>

      <!-- Terms Checkbox -->
      <div class="form-group" style="margin-top: 0.5rem;">
        <label style="display: flex; align-items: flex-start; gap: 0.6rem; cursor: pointer; font-size: 0.9rem;">
          <input type="checkbox" id="terms_agree" name="terms_agree" style="margin-top: 0.2rem;" required>
          <span>I agree to abide by the university academic code of conduct and privacy terms.</span>
        </label>
        <div class="invalid-feedback" style="<?= isset($errors['terms']) ? 'display: block;' : '' ?>">
          <?= e($errors['terms'] ?? 'You must agree to the academic terms.') ?>
        </div>
      </div>

      <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem; padding: 0.75rem;">
        &#x2705; Register Account
      </button>

      <div style="text-align: center; margin-top: 1.5rem; font-size: 0.9rem; color: var(--text-muted);">
        Already have a registered account? 
        <a href="<?= base_url('login.php') ?>" style="font-weight: 700;">Sign in here</a>
      </div>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
