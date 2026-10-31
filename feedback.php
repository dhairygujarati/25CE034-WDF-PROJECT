<?php
/**
 * CHARUSAT Student Portal - Student Feedback Form
 * Form fields match the official portal feedback structure.
 */

$pageTitle = 'Student Feedback';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/csrf.php';

$errors = [];
$successMessage = '';

// Process POST Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (has_role('admin')) {
        set_flash('danger', 'Administrators can review student feedback but cannot submit feedback.');
        header('Location: ' . base_url('feedback.php'));
        exit;
    }

    require_csrf();

    $name        = trim($_POST['name'] ?? '');
    $studentId   = trim($_POST['student_id'] ?? '');
    $department  = trim($_POST['department'] ?? '');
    $semester    = trim($_POST['semester'] ?? '');
    $portalRate  = trim($_POST['rate_portal'] ?? '');
    $suggestions = trim($_POST['suggestions'] ?? '');

    // Server-Side Validations
    if (empty($name) || mb_strlen($name) < 2) {
        $errors['name'] = 'Please enter your name.';
    }

    if (empty($studentId)) {
        $errors['student_id'] = 'Please enter your Student ID.';
    }

    if (empty($department)) {
        $errors['department'] = 'Please enter your Department.';
    }

    if (empty($semester)) {
        $errors['semester'] = 'Please select your Semester.';
    }

    if (empty($portalRate)) {
        $errors['rate_portal'] = 'Please rate our Student Portal.';
    }

    if (empty($suggestions) || mb_strlen($suggestions) < 5) {
        $errors['suggestions'] = 'Please write your feedback/suggestions.';
    }

    // Store feedback in JSON and CSV formats
    if (empty($errors)) {
        $storageDir = __DIR__ . '/storage';
        if (!is_dir($storageDir)) {
            mkdir($storageDir, 0755, true);
        }

        $timestamp  = date('Y-m-d H:i:s');
        $ipAddress  = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $feedbackId = 'FDB-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));

        $record = [
            'id'          => $feedbackId,
            'timestamp'   => $timestamp,
            'name'        => htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
            'student_id'  => htmlspecialchars($studentId, ENT_QUOTES, 'UTF-8'),
            'department'  => htmlspecialchars($department, ENT_QUOTES, 'UTF-8'),
            'semester'    => htmlspecialchars($semester, ENT_QUOTES, 'UTF-8'),
            'rating'      => htmlspecialchars($portalRate, ENT_QUOTES, 'UTF-8'),
            'suggestions' => htmlspecialchars($suggestions, ENT_QUOTES, 'UTF-8'),
            'ip_address'  => $ipAddress
        ];

        // 1. Append to inquiries.csv
        $csvFile = $storageDir . '/inquiries.csv';
        $isNewCsv = !file_exists($csvFile) || filesize($csvFile) === 0;
        
        $fp = fopen($csvFile, 'a');
        if ($fp) {
            flock($fp, LOCK_EX);
            if ($isNewCsv) {
                fputcsv($fp, ['Feedback_ID', 'Timestamp', 'Name', 'Student_ID', 'Department', 'Semester', 'Rating', 'Suggestions', 'IP_Address']);
            }
            fputcsv($fp, array_values($record));
            flock($fp, LOCK_UN);
            fclose($fp);
        }

        // 2. Append to inquiries.json
        $jsonFile = $storageDir . '/inquiries.json';
        $jsonRecords = [];
        if (file_exists($jsonFile)) {
            $rawJson = file_get_contents($jsonFile);
            $decoded = json_decode($rawJson, true);
            if (is_array($decoded)) {
                $jsonRecords = $decoded;
            }
        }
        $jsonRecords[] = $record;
        file_put_contents($jsonFile, json_encode($jsonRecords, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);

        log_activity(current_user()['id'] ?? null, 'SUBMIT_FEEDBACK', "Feedback $feedbackId submitted by $name ($studentId)");

        set_flash('success', "Thank you! Your feedback (#{$feedbackId}) has been successfully submitted.");
        header('Location: ' . base_url('feedback.php?submitted=1'));
        exit;
    }
}

// Load feedback entries for Admin review
$adminFeedbacks = [];
if (has_role('admin')) {
    $jsonFile = __DIR__ . '/storage/inquiries.json';
    if (file_exists($jsonFile)) {
        $raw = file_get_contents($jsonFile);
        $adminFeedbacks = json_decode($raw, true) ?? [];
        $adminFeedbacks = array_reverse($adminFeedbacks);
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="container" style="max-width: 600px; margin: 0 auto;">

  <?php if (!has_role('admin')): ?>
  <div class="card" style="border: 1px solid var(--border-color); border-radius: var(--radius-md); box-shadow: var(--shadow-sm); padding: 1.75rem 2rem; margin-bottom: 2.5rem; background: #ffffff;">

    <form method="POST" action="<?= base_url('feedback.php') ?>" novalidate>
      <?= csrf_field() ?>

      <!-- Name -->
      <div class="form-group" style="margin-bottom: 1.25rem;">
        <label for="name" style="display: block; font-weight: 700; color: #0a2558; font-size: 0.95rem; margin-bottom: 0.4rem;">
          Name
        </label>
        <input 
          type="text" 
          id="name" 
          name="name" 
          class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
          placeholder="Enter your name"
          value="<?= e($_POST['name'] ?? (current_user()['name'] ?? '')) ?>"
          style="border: 1px solid #cbd5e1; border-radius: 4px; padding: 0.6rem 0.8rem; font-size: 0.9rem;"
          required
        >
        <?php if (isset($errors['name'])): ?>
          <div class="invalid-feedback"><?= e($errors['name']) ?></div>
        <?php endif; ?>
      </div>

      <!-- Student ID -->
      <div class="form-group" style="margin-bottom: 1.25rem;">
        <label for="student_id" style="display: block; font-weight: 700; color: #0a2558; font-size: 0.95rem; margin-bottom: 0.4rem;">
          Student ID
        </label>
        <input 
          type="text" 
          id="student_id" 
          name="student_id" 
          class="form-control <?= isset($errors['student_id']) ? 'is-invalid' : '' ?>" 
          placeholder="Enter Student ID"
          value="<?= e($_POST['student_id'] ?? '') ?>"
          style="border: 1px solid #cbd5e1; border-radius: 4px; padding: 0.6rem 0.8rem; font-size: 0.9rem;"
          required
        >
        <?php if (isset($errors['student_id'])): ?>
          <div class="invalid-feedback"><?= e($errors['student_id']) ?></div>
        <?php endif; ?>
      </div>

      <!-- Department -->
      <div class="form-group" style="margin-bottom: 1.25rem;">
        <label for="department" style="display: block; font-weight: 700; color: #0a2558; font-size: 0.95rem; margin-bottom: 0.4rem;">
          Department
        </label>
        <input 
          type="text" 
          id="department" 
          name="department" 
          class="form-control <?= isset($errors['department']) ? 'is-invalid' : '' ?>" 
          placeholder="Enter Department"
          value="<?= e($_POST['department'] ?? '') ?>"
          style="border: 1px solid #cbd5e1; border-radius: 4px; padding: 0.6rem 0.8rem; font-size: 0.9rem;"
          required
        >
        <?php if (isset($errors['department'])): ?>
          <div class="invalid-feedback"><?= e($errors['department']) ?></div>
        <?php endif; ?>
      </div>

      <!-- Semester -->
      <div class="form-group" style="margin-bottom: 1.25rem;">
        <label for="semester" style="display: block; font-weight: 700; color: #0a2558; font-size: 0.95rem; margin-bottom: 0.4rem;">
          Semester
        </label>
        <select 
          id="semester" 
          name="semester" 
          class="form-control <?= isset($errors['semester']) ? 'is-invalid' : '' ?>"
          style="border: 1px solid #cbd5e1; border-radius: 4px; padding: 0.6rem 0.8rem; font-size: 0.9rem;"
          required
        >
          <option value="">Select Semester</option>
          <option value="Semester 1" <?= (($_POST['semester'] ?? '') === 'Semester 1') ? 'selected' : '' ?>>Semester 1</option>
          <option value="Semester 2" <?= (($_POST['semester'] ?? '') === 'Semester 2') ? 'selected' : '' ?>>Semester 2</option>
          <option value="Semester 3" <?= (($_POST['semester'] ?? '') === 'Semester 3') ? 'selected' : '' ?>>Semester 3</option>
          <option value="Semester 4" <?= (($_POST['semester'] ?? '') === 'Semester 4') ? 'selected' : '' ?>>Semester 4</option>
          <option value="Semester 5" <?= (($_POST['semester'] ?? '') === 'Semester 5') ? 'selected' : '' ?>>Semester 5</option>
          <option value="Semester 6" <?= (($_POST['semester'] ?? '') === 'Semester 6') ? 'selected' : '' ?>>Semester 6</option>
          <option value="Semester 7" <?= (($_POST['semester'] ?? '') === 'Semester 7') ? 'selected' : '' ?>>Semester 7</option>
          <option value="Semester 8" <?= (($_POST['semester'] ?? '') === 'Semester 8') ? 'selected' : '' ?>>Semester 8</option>
        </select>
        <?php if (isset($errors['semester'])): ?>
          <div class="invalid-feedback"><?= e($errors['semester']) ?></div>
        <?php endif; ?>
      </div>

      <!-- Rate Our Student Portal -->
      <div class="form-group" style="margin-bottom: 1.25rem;">
        <label style="display: block; font-weight: 700; color: #0a2558; font-size: 0.95rem; margin-bottom: 0.5rem;">
          Rate Our Student Portal
        </label>
        <div style="display: flex; flex-direction: column; gap: 0.45rem; padding-left: 0.25rem;">
          <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.92rem; color: var(--text-main); cursor: pointer;">
            <input type="radio" name="rate_portal" value="Excellent" <?= (($_POST['rate_portal'] ?? '') === 'Excellent') ? 'checked' : '' ?> required>
            <span>Excellent</span>
          </label>
          <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.92rem; color: var(--text-main); cursor: pointer;">
            <input type="radio" name="rate_portal" value="Good" <?= (($_POST['rate_portal'] ?? '') === 'Good') ? 'checked' : '' ?>>
            <span>Good</span>
          </label>
          <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.92rem; color: var(--text-main); cursor: pointer;">
            <input type="radio" name="rate_portal" value="Average" <?= (($_POST['rate_portal'] ?? '') === 'Average') ? 'checked' : '' ?>>
            <span>Average</span>
          </label>
          <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.92rem; color: var(--text-main); cursor: pointer;">
            <input type="radio" name="rate_portal" value="Poor" <?= (($_POST['rate_portal'] ?? '') === 'Poor') ? 'checked' : '' ?>>
            <span>Poor</span>
          </label>
        </div>
        <?php if (isset($errors['rate_portal'])): ?>
          <div class="invalid-feedback" style="display: block;"><?= e($errors['rate_portal']) ?></div>
        <?php endif; ?>
      </div>

      <!-- Suggestions -->
      <div class="form-group" style="margin-bottom: 1.5rem;">
        <label for="suggestions" style="display: block; font-weight: 700; color: #0a2558; font-size: 0.95rem; margin-bottom: 0.4rem;">
          Suggestions
        </label>
        <textarea 
          id="suggestions" 
          name="suggestions" 
          class="form-control <?= isset($errors['suggestions']) ? 'is-invalid' : '' ?>" 
          rows="4"
          placeholder="Write your feedback here..." 
          style="border: 1px solid #cbd5e1; border-radius: 4px; padding: 0.6rem 0.8rem; font-size: 0.9rem; min-height: 100px; resize: vertical;"
          required
        ><?= e($_POST['suggestions'] ?? '') ?></textarea>
        <?php if (isset($errors['suggestions'])): ?>
          <div class="invalid-feedback"><?= e($errors['suggestions']) ?></div>
        <?php endif; ?>
      </div>

      <!-- Submit Button -->
      <button 
        type="submit" 
        style="width: 100%; background: #000080; color: #ffffff; border: none; padding: 0.75rem; border-radius: 4px; font-weight: 700; font-size: 1rem; cursor: pointer; margin-bottom: 0.65rem;"
      >
        Submit
      </button>

      <!-- Back Button -->
      <a 
        href="<?= base_url('index.php') ?>" 
        style="display: block; text-align: center; width: 100%; background: #000080; color: #ffffff; text-decoration: none; padding: 0.75rem; border-radius: 4px; font-weight: 700; font-size: 1rem;"
      >
        Back
      </a>

    </form>
  </div>
  <?php endif; ?>

  <!-- Admin Live Review Table (Visible to logged in admins) -->
  <?php if (has_role('admin')): ?>
    <section class="card" style="margin-top: 2rem; border-radius: var(--radius-md);">
      <div class="card-header">
        <h3 style="font-size: 1.1rem; margin: 0;">Student Feedback (Admin View)</h3>
        <span class="badge badge-info"><?= count($adminFeedbacks) ?> Total</span>
      </div>
      <div class="card-body" style="padding: 0;">
        <?php if (empty($adminFeedbacks)): ?>
          <p style="padding: 1.5rem; text-align: center; color: var(--text-muted); margin: 0;">No student feedback recorded yet.</p>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table" style="margin-bottom: 0;">
              <thead>
                <tr>
                  <th>Rating</th>
                  <th>Student Feedback</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($adminFeedbacks as $fdb): ?>
                  <tr>
                    <td><span class="badge badge-primary"><?= e($fdb['rating'] ?? '') ?></span></td>
                    <td style="font-size: 0.88rem;"><?= e($fdb['suggestions'] ?? ($fdb['message'] ?? '')) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
    </section>
  <?php endif; ?>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
