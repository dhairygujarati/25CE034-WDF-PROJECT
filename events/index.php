<?php
/**
 * Campus Event Management - List & Showcase
 * Item 12: Event Management CRUD with Poster Upload & File Validation
 */

$pageTitle = 'Campus Events & Workshops';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/csrf.php';

$pdo = Database::getConnection();

$category = trim($_GET['category'] ?? '');
$sql = "SELECT * FROM events WHERE 1=1";
$params = [];

if ($category !== '') {
    $sql .= " AND category = :cat";
    $params[':cat'] = $category;
}

$sql .= " ORDER BY event_date ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$events = $stmt->fetchAll();

$isAdmin = has_role('admin');

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
    <div>
      <span class="badge badge-primary">Campus Events</span>
      <h1 style="margin-top: 0.4rem;">Campus Events &amp; Activities</h1>
    </div>

    <?php if ($isAdmin): ?>
      <a href="<?= base_url('events/create.php') ?>" class="btn btn-primary">
        &#x2B; Publish New Event
      </a>
    <?php elseif (!is_logged_in()): ?>
      <a href="<?= base_url('login.php') ?>" class="btn btn-primary">
        Sign In as Admin to Publish
      </a>
    <?php else: ?>
      <span class="btn btn-outline" role="note">
        Admin access required to publish events
      </span>
    <?php endif; ?>
  </div>

  <!-- Category Filter Toolbar -->
  <form method="GET" action="<?= base_url('events/index.php') ?>" class="toolbar-controls" role="search">
    <div style="font-weight: 700; color: var(--text-main);">
      Filter by Category:
    </div>

    <div class="toolbar-filters">
      <select name="category" class="form-control" onchange="this.form.submit()" aria-label="Select Event Category">
        <option value="">All Categories (<?= count($events) ?>)</option>
        <option value="Hackathon" <?= ($category === 'Hackathon') ? 'selected' : '' ?>>Hackathons</option>
        <option value="Seminar" <?= ($category === 'Seminar') ? 'selected' : '' ?>>Seminars &amp; Talks</option>
        <option value="Workshop" <?= ($category === 'Workshop') ? 'selected' : '' ?>>Hands-on Workshops</option>
        <option value="Cultural" <?= ($category === 'Cultural') ? 'selected' : '' ?>>Cultural &amp; Arts</option>
      </select>

      <?php if ($category !== ''): ?>
        <a href="<?= base_url('events/index.php') ?>" class="btn btn-outline btn-sm">Clear Filter</a>
      <?php endif; ?>
    </div>
  </form>

  <!-- Events Showcase Grid (CSS Grid - Item 3 & 12) -->
  <div class="events-grid">
    <?php if (empty($events)): ?>
      <div style="grid-column: 1 / -1; text-align: center; padding: 4rem; background: var(--bg-surface); border: 1px dashed var(--border-color); border-radius: var(--radius-lg);">
        <div style="font-size: 3rem; margin-bottom: 0.5rem;">&#x1F4C5;</div>
        <h3>No Events Found</h3>
        <p style="color: var(--text-muted);">No campus events currently scheduled in this category.</p>
        <?php if ($isAdmin): ?>
          <a href="<?= base_url('events/create.php') ?>" class="btn btn-primary btn-sm" style="margin-top: 1rem;">
            Create First Event
          </a>
        <?php endif; ?>
      </div>
    <?php else: ?>
      <?php foreach ($events as $ev): ?>
        <article class="card event-card">
          <!-- Poster Thumbnail Preview -->
          <div class="poster-container">
            <?php 
              $posterUrl = !empty($ev['poster_url']) ? base_url($ev['poster_url']) : base_url('assets/images/sample_poster1.svg');
            ?>
            <img src="<?= $posterUrl ?>" alt="Event Poster for <?= e($ev['title']) ?>" loading="lazy">
          </div>

          <div class="card-body">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
              <span class="badge badge-info"><?= e($ev['category']) ?></span>
              <span style="font-size: 0.85rem; font-weight: 600; color: var(--accent);">
                👥 Max <?= (int)$ev['max_participants'] ?>
              </span>
            </div>

            <h2 style="font-size: 1.25rem; margin-bottom: 0.5rem; color: var(--text-main);">
              <?= e($ev['title']) ?>
            </h2>

            <p style="font-size: 0.88rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 1rem;">
              <?= e($ev['description']) ?>
            </p>

            <div style="border-top: 1px solid var(--border-subtle); padding-top: 0.75rem; font-size: 0.85rem; color: var(--text-main); display: flex; flex-direction: column; gap: 0.35rem;">
              <div>📅 <strong>Date:</strong> <?= date('F d, Y', strtotime($ev['event_date'])) ?> at <?= date('h:i A', strtotime($ev['event_time'])) ?></div>
              <div>📍 <strong>Venue:</strong> <?= e($ev['venue']) ?></div>
              <div>🏛️ <strong>Organizer:</strong> <?= e($ev['organizer']) ?></div>
            </div>
          </div>

          <?php if ($isAdmin): ?>
            <div class="card-footer" style="display: flex; justify-content: flex-end; gap: 0.5rem; background: var(--bg-alt);">
              <a href="<?= base_url('events/edit.php?id=' . $ev['id']) ?>" class="btn btn-outline btn-sm">
                Edit
              </a>
              <form method="POST" action="<?= base_url('events/delete.php') ?>" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this event and remove its poster?');">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= $ev['id'] ?>">
                <button type="submit" class="btn btn-danger btn-sm">
                  Delete
                </button>
              </form>
            </div>
          <?php endif; ?>
        </article>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
