<?php
/**
 * Event Management - Edit Event & Replace Poster
 * Item 12: Event CRUD with File Validation and Safe File Replacement
 */

$pageTitle = 'Edit Campus Event';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/csrf.php';

require_role('admin');

$pdo = Database::getConnection();
$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    set_flash('danger', 'Invalid event ID.');
    header('Location: ' . base_url('events/index.php'));
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM events WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $id]);
$event = $stmt->fetch();

if (!$event) {
    set_flash('danger', 'Event not found.');
    header('Location: ' . base_url('events/index.php'));
    exit;
}

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

    if (empty($title))       $errors['title'] = 'Event title is required.';
    if (empty($category))    $errors['category'] = 'Please select a category.';
    if (empty($description)) $errors['description'] = 'Description is required.';
    if (empty($eventDate))   $errors['event_date'] = 'Date is required.';
    if (empty($eventTime))   $errors['event_time'] = 'Time is required.';
    if (empty($venue))       $errors['venue'] = 'Venue is required.';
    if (empty($organizer))   $errors['organizer'] = 'Organizer is required.';

    $posterPath = $event['poster_url']; // Keep existing by default

    // If a new poster is uploaded
    if (isset($_FILES['poster']) && $_FILES['poster']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['poster'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors['poster'] = 'File upload error code: ' . $file['error'];
        } else {
            // Size limit 2MB
            if ($file['size'] > 2 * 1024 * 1024) {
                $errors['poster'] = 'Poster file size exceeds the 2MB limit.';
            }

            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
                $errors['poster'] = 'Invalid file extension. Only JPG, PNG, and WebP are allowed.';
            }

            if (empty($errors['poster'])) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_file($finfo, $file['tmp_name']);
                finfo_close($finfo);

                if (!in_array($mimeType, ['image/jpeg', 'image/png', 'image/webp'], true)) {
                    $errors['poster'] = 'Invalid file format. Uploaded file is not a supported image.';
                }
            }

            if (empty($errors['poster'])) {
                $uploadDir = __DIR__ . '/../uploads/posters';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $safeFilename = 'poster_' . bin2hex(random_bytes(8)) . '.' . $ext;
                $targetDest   = $uploadDir . '/' . $safeFilename;

                if (move_uploaded_file($file['tmp_name'], $targetDest)) {
                    // Safe cleanup: delete old poster file if located in uploads/posters/
                    if (!empty($event['poster_url']) && strpos($event['poster_url'], 'uploads/posters/') !== false) {
                        $oldFile = __DIR__ . '/../' . $event['poster_url'];
                        if (file_exists($oldFile)) {
                            @unlink($oldFile);
                        }
                    }
                    $posterPath = 'uploads/posters/' . $safeFilename;
                } else {
                    $errors['poster'] = 'Failed to safely relocate uploaded poster.';
                }
            }
        }
    }

    if (empty($errors)) {
        try {
            $update = $pdo->prepare("
                UPDATE events SET
                    title = :title,
                    category = :cat,
                    description = :desc,
                    event_date = :date,
                    event_time = :time,
                    venue = :venue,
                    organizer = :org,
                    max_participants = :max,
                    poster_url = :poster
                WHERE id = :id
            ");
            $update->execute([
                ':title'  => $title,
                ':cat'    => $category,
                ':desc'   => $description,
                ':date'   => $eventDate,
                ':time'   => $eventTime,
                ':venue'  => $venue,
                ':org'    => $organizer,
                ':max'    => $maxCapacity,
                ':poster' => $posterPath,
                ':id'     => $id
            ]);

            log_activity(current_user()['id'], 'UPDATE_EVENT', "Updated event #$id ($title)");
            set_flash('success', "Event '{$title}' updated successfully!");
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
    <h1 style="margin-top: 0.5rem;">Edit Event: <?= e($event['title']) ?></h1>
    <p style="color: var(--text-muted);">Modify event information and optionally replace poster graphic.</p>
  </div>

  <?php if (!empty($errors['general'])): ?>
    <div class="alert alert-danger" role="alert">
      <?= e($errors['general']) ?>
    </div>
  <?php endif; ?>

  <div class="card">
    <div class="card-body">
      <form method="POST" action="<?= base_url('events/edit.php?id=' . $id) ?>"  novalidate>
        <?= csrf_field() ?>

        <div class="form-row-2">
          </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
