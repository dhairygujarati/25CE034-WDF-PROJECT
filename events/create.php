<?php
/**
 * Event Management - Create Event with Secure Poster Upload
 * Item 12: Event Management CRUD with Poster Upload and File Validation
 */

$pageTitle = 'Create Campus Event';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/csrf.php';

require_role('admin');

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();

    $title       = trim($_POST['title'] ?? '');
    $category    = trim($_POST['category'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $eventDate   = trim($_POST['event_date'] ?? '');
    $eventTime   = trim($_POST['event_time'] ?? '');
    $venue       = trim($_POST['venue'] ?? '');
    $organizer   = trim($_POST['organizer'] ?? '');
    $maxCapacity = (int)($_POST['max_participants'] ?? 100);

    // Text field validations
    if (empty($title))       $errors['title'] = 'Event title is required.';
    if (empty($category))    $errors['category'] = 'Please select a category.';
    if (empty($description)) $errors['description'] = 'Event description is required.';
    if (empty($eventDate))   $errors['event_date'] = 'Event date is required.';
    if (empty($eventTime))   $errors['event_time'] = 'Event time is required.';
    if (empty($venue))       $errors['venue'] = 'Venue location is required.';
    if (empty($organizer))   $errors['organizer'] = 'Organizer details are required.';

    // Poster Image Upload & Validation (Item 12 Spec)
    $posterPath = 'assets/images/sample_poster1.svg'; // Default fallback

    if (isset($_FILES['poster']) && $_FILES['poster']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['poster'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors['poster'] = 'File upload error code: ' . $file['error'];
        } else {
            // 1. File Size Validation (Max 2MB = 2,097,152 bytes)
            $maxFileSize = 2 * 1024 * 1024;
            if ($file['size'] > $maxFileSize) {
                $errors['poster'] = 'Poster file size exceeds the 2MB limit.';
            }

            // 2. Extension Whitelist Validation
            $origName = $file['name'];
            $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
            if (!in_array($ext, $allowedExtensions, true)) {
                $errors['poster'] = 'Invalid file extension. Only JPG, PNG, and WebP images are permitted.';
            }

            // 3. MIME Type Verification using finfo
            if (empty($errors['poster'])) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_file($finfo, $file['tmp_name']);
                finfo_close($finfo);

                $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
                if (!in_array($mimeType, $allowedMimes, true)) {
                    $errors['poster'] = "Invalid file type ({$mimeType}). Only valid image files are allowed.";
                }
            }

            // 4. Secure Random File Naming & Relocation
            if (empty($errors['poster'])) {
                $uploadDir = __DIR__ . '/../uploads/posters';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $safeFilename = 'poster_' . bin2hex(random_bytes(8)) . '.' . $ext;
                $targetDest   = $uploadDir . '/' . $safeFilename;

                if (move_uploaded_file($file['tmp_name'], $targetDest)) {
                    $posterPath = 'uploads/posters/' . $safeFilename;
                } else {
                    $errors['poster'] = 'Failed to safely move uploaded poster file.';
                }
            }
        }
    }

    // Insert into MySQL via Prepared Statement
    if (empty($errors)) {
        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("
                INSERT INTO events (title, category, description, event_date, event_time, venue, organizer, max_participants, poster_url, created_by)
                VALUES (:title, :cat, :desc, :date, :time, :venue, :org, :max, :poster, :uid)
            ");
            $stmt->execute([
                ':title'  => $title,
                ':cat'    => $category,
                ':desc'   => $description,
                ':date'   => $eventDate,
                ':time'   => $eventTime,
                ':venue'  => $venue,
                ':org'    => $organizer,
                ':max'    => $maxCapacity,
                ':poster' => $posterPath,
                ':uid'    => current_user()['id']
            ]);

            $newId = (int)$pdo->lastInsertId();
            log_activity(current_user()['id'], 'CREATE_EVENT', "Created event #$newId ($title)");

            set_flash('success', "Event '{$title}' published successfully!");
            header('Location: ' . base_url('events/index.php'));
            exit;
        } catch (Exception $e) {
            $errors['general'] = 'Database Error: ' . $e->getMessage();
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container" style="max-width: 820px;">
  <div style="margin-bottom: 2rem;">
    <a href="<?= base_url('events/index.php') ?>" style="font-weight: 600; font-size: 0.9rem;">&larr; Back to Events</a>
    <h1 style="margin-top: 0.5rem;">Create New Campus Event</h1>
    <p style="color: var(--text-muted);">Publish an academic seminar, competition, or workshop with poster image upload.</p>
  </div>

  <?php if (!empty($errors['general'])): ?>
    <div class="alert alert-danger" role="alert">
      <?= e($errors['general']) ?>
    </div>
  <?php endif; ?>

  <div class="card">
    <div class="card-body">
      <form method="POST" action="<?= base_url('events/create.php') ?>"  novalidate>
        <?= csrf_field() ?>

        <div class="form-row-2">
          <div class="form-group">
            <label for="title" class="form-label">Event Title <span class="required">*</span></label>
            <input type="text" id="title" name="title" class="form-control <?= isset($errors['title']) ? 'is-invalid' : '' ?>" value="<?= e($_POST['title'] ?? '') ?>" placeholder="e.g. AI & Robotics Symposium" required>
            <?php if (isset($errors['title'])): ?><div class="invalid-feedback"><?= e($errors['title']) ?></div><?php endif; ?>
          </div>

          <div class="form-group">
            <label for="category" class="form-label">Category <span class="required">*</span></label>
            <select id="category" name="category" class="form-control <?= isset($errors['category']) ? 'is-invalid' : '' ?>" required>
              <option value="">Select Category</option>
              <?php foreach (['Hackathon', 'Seminar', 'Workshop', 'Cultural'] as $eventCategory): ?>
                <option value="<?= e($eventCategory) ?>" <?= (($_POST['category'] ?? '') === $eventCategory) ? 'selected' : '' ?>><?= e($eventCategory) ?></option>
              <?php endforeach; ?>
            </select>
            <?php if (isset($errors['category'])): ?><div class="invalid-feedback"><?= e($errors['category']) ?></div><?php endif; ?>
          </div>
        </div>

        <div class="form-group">
          <label for="description" class="form-label">Detailed Description <span class="required">*</span></label>
          <textarea id="description" name="description" class="form-control <?= isset($errors['description']) ? 'is-invalid' : '' ?>" placeholder="Provide agenda, prerequisites, and keynote speakers..." required><?= e($_POST['description'] ?? '') ?></textarea>
          <?php if (isset($errors['description'])): ?><div class="invalid-feedback"><?= e($errors['description']) ?></div><?php endif; ?>
        </div>

        <div class="form-row-3">
          <div class="form-group">
            <label for="event_date" class="form-label">Event Date <span class="required">*</span></label>
            <input type="date" id="event_date" name="event_date" class="form-control <?= isset($errors['event_date']) ? 'is-invalid' : '' ?>" value="<?= e($_POST['event_date'] ?? date('Y-m-d', strtotime('+7 days'))) ?>" required>
            <?php if (isset($errors['event_date'])): ?><div class="invalid-feedback"><?= e($errors['event_date']) ?></div><?php endif; ?>
          </div>

          <div class="form-group">
            <label for="event_time" class="form-label">Event Time <span class="required">*</span></label>
            <input type="time" id="event_time" name="event_time" class="form-control <?= isset($errors['event_time']) ? 'is-invalid' : '' ?>" value="<?= e($_POST['event_time'] ?? '10:00') ?>" required>
            <?php if (isset($errors['event_time'])): ?><div class="invalid-feedback"><?= e($errors['event_time']) ?></div><?php endif; ?>
          </div>

          <div class="form-group">
            <label for="max_participants" class="form-label">Max Participants <span class="required">*</span></label>
            <input type="number" id="max_participants" name="max_participants" min="1" max="5000" class="form-control" value="<?= e($_POST['max_participants'] ?? '100') ?>" required>
          </div>
        </div>

        <div class="form-row-2">
          <div class="form-group">
            <label for="venue" class="form-label">Venue Location <span class="required">*</span></label>
            <input type="text" id="venue" name="venue" class="form-control <?= isset($errors['venue']) ? 'is-invalid' : '' ?>" value="<?= e($_POST['venue'] ?? '') ?>" placeholder="e.g. Science Auditorium / Lab 2" required>
            <?php if (isset($errors['venue'])): ?><div class="invalid-feedback"><?= e($errors['venue']) ?></div><?php endif; ?>
          </div>

          <div class="form-group">
            <label for="organizer" class="form-label">Organizing Club / Dept <span class="required">*</span></label>
            <input type="text" id="organizer" name="organizer" class="form-control <?= isset($errors['organizer']) ? 'is-invalid' : '' ?>" value="<?= e($_POST['organizer'] ?? '') ?>" placeholder="e.g. IEEE Student Chapter" required>
            <?php if (isset($errors['organizer'])): ?><div class="invalid-feedback"><?= e($errors['organizer']) ?></div><?php endif; ?>
          </div>
        </div>

        <div class="form-group" style="padding: 1rem; background: var(--bg-alt); border-radius: var(--radius-md); border: 1px dashed var(--border-color);">
          <label for="poster" class="form-label">Event Poster Image (Max 2MB: JPG, PNG, WebP)</label>
          <input
            type="file"
            id="poster"
            name="poster"
            class="form-control <?= isset($errors['poster']) ? 'is-invalid' : '' ?>"
            accept="image/jpeg,image/png,image/webp"
          >
          <div class="form-hint">
            Upload an optional JPG, PNG, or WebP image. Maximum file size: 2MB.
          </div>
          <?php if (isset($errors['poster'])): ?>
            <div class="invalid-feedback" style="display: block;"><?= e($errors['poster']) ?></div>
          <?php endif; ?>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem;">
          <a href="<?= base_url('events/index.php') ?>" class="btn btn-outline">Cancel</a>
          <button type="submit" class="btn btn-primary">&#x2713; Save Event Record</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
