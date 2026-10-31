<?php
/**
 * Campus Portal - Admin & Student Dashboard
 */

$pageTitle = 'Dashboard';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/database.php';

require_login(); // Access Guard

$user = current_user();
$isAdmin = has_role('admin');

// Fetch dashboard metrics
$pdo = Database::getConnection();

$totalStudents = (int)$pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
$totalEvents   = (int)$pdo->query("SELECT COUNT(*) FROM events")->fetchColumn();
$totalUsers    = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

// Count inquiries from JSON
$inquiryCount = 0;
$jsonFile = __DIR__ . '/storage/inquiries.json';
if (file_exists($jsonFile)) {
    $inqData = json_decode(file_get_contents($jsonFile), true);
    if (is_array($inqData)) {
        $inquiryCount = count($inqData);
    }
}

// Fetch upcoming events
$upcomingEvents = $pdo->query("
    SELECT id, title, category, event_date, event_time, venue 
    FROM events 
    ORDER BY event_date ASC 
    LIMIT 4
")->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<div class="container">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
    <div>
      <span class="badge <?= $isAdmin ? 'badge-primary' : 'badge-info' ?>">
        <?= $isAdmin ? 'Administrator Console' : 'Student Portal' ?>
      </span>
      <h1 style="margin-top: 0.4rem;">
        Welcome back, <?= e($user['name']) ?>!
      </h1>
      <p style="color: var(--text-muted); font-size: 0.95rem;">
        Role: <strong><?= ucfirst(e($user['role'])) ?></strong> &bull; Signed in as <?= e($user['email']) ?>
      </p>
    </div>

    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
      <?php if ($isAdmin): ?>
        <a href="<?= base_url('students/create.php') ?>" class="btn btn-primary btn-sm">
          &#x2B; Add Student
        </a>
        <a href="<?= base_url('events/create.php') ?>" class="btn btn-accent btn-sm">
          &#x2B; Create Event
        </a>
        
      <?php else: ?>
        <a href="<?= base_url('explore.php') ?>" class="btn btn-primary btn-sm">
          &#x1F50D; Browse Courses
        </a>
        <a href="<?= base_url('events/index.php') ?>" class="btn btn-outline btn-sm">
          &#x1F4C5; View Events
        </a>
      <?php endif; ?>
    </div>
  </div>

  <!-- Dashboard Layout: Sidebar + Content -->
  <div class="dashboard-layout">
    <!-- Sidebar Navigation -->
    <aside class="dashboard-sidebar" aria-label="Dashboard Sub-navigation">
      <h3 style="font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 0.75rem;">
        Navigation Menu
      </h3>
      <ul class="sidebar-menu">
        <li>
          <a href="<?= base_url('dashboard.php') ?>" class="sidebar-link active">
            &#x1F4CA; Overview
          </a>
        </li>

        <?php if ($isAdmin): ?>
          <li>
            <a href="<?= base_url('students/index.php') ?>" class="sidebar-link">
              &#x1F468;&#x200D;&#x1F393; Students Management
            </a>
          </li>
          <li>
            <a href="<?= base_url('events/index.php') ?>" class="sidebar-link">
              &#x1F3A8; Events Management
            </a>
          </li>
          
          <li>
            <a href="<?= base_url('feedback.php') ?>" class="sidebar-link">
              &#x1F4E5; Student Feedback (<?= $inquiryCount ?>)
            </a>
          </li>
        <?php else: ?>
          <li>
            <a href="<?= base_url('explore.php') ?>" class="sidebar-link">
              &#x1F4DA; Course Catalog
            </a>
          </li>
          <li>
            <a href="<?= base_url('events/index.php') ?>" class="sidebar-link">
              &#x1F389; Campus Events
            </a>
          </li>
          <li>
            <a href="<?= base_url('feedback.php') ?>" class="sidebar-link">
              &#x2709;&#xFE0F; Submit Feedback
            </a>
          </li>
        <?php endif; ?>

        <li style="margin-top: 1rem; border-top: 1px solid var(--border-color); padding-top: 0.75rem;">
          <a href="<?= base_url('logout.php') ?>" class="sidebar-link" style="color: var(--danger);">
            &#x1F6AA; Sign Out
          </a>
        </li>
      </ul>
    </aside>

    <!-- Main Dashboard Content -->
    <section>
      <!-- Metric Cards -->
      <div class="metrics-grid">
        <div class="metric-card">
          <div class="metric-icon" style="background: #dbeafe; color: #1d4ed8;">&#x1F393;</div>
          <div class="metric-data">
            <div class="metric-value"><?= $totalStudents ?></div>
            <div class="metric-label">Enrolled Students</div>
          </div>
        </div>

        <div class="metric-card">
          <div class="metric-icon" style="background: #fef3c7; color: #b45309;">&#x1F388;</div>
          <div class="metric-data">
            <div class="metric-value"><?= $totalEvents ?></div>
            <div class="metric-label">Active Events</div>
          </div>
        </div>

        <div class="metric-card">
          <div class="metric-icon" style="background: #dcfce7; color: #15803d;">&#x1F4E8;</div>
          <div class="metric-data">
            <div class="metric-value"><?= $inquiryCount ?></div>
            <div class="metric-label">Feedback Submissions</div>
          </div>
        </div>

        <div class="metric-card">
          <div class="metric-icon" style="background: #f3e8ff; color: #7e22ce;">&#x1F465;</div>
          <div class="metric-data">
            <div class="metric-value"><?= $totalUsers ?></div>
            <div class="metric-label">Registered Accounts</div>
          </div>
        </div>
      </div>

      <!-- Upcoming Events -->
      <div class="card" style="margin-bottom: 2rem;">
        <div class="card-header">
          <h3>&#x1F4C5; Upcoming Events</h3>
          <a href="<?= base_url('events/index.php') ?>" style="font-size: 0.85rem; font-weight: 600;">View All</a>
        </div>
        <div class="card-body" style="padding: 0;">
          <ul style="list-style: none;">
            <?php foreach ($upcomingEvents as $ev): ?>
              <li style="padding: 0.85rem 1.25rem; border-bottom: 1px solid var(--border-subtle); display: flex; justify-content: space-between; align-items: center;">
                <div>
                  <strong style="display: block; font-size: 0.95rem; color: var(--text-main);"><?= e($ev['title']) ?></strong>
                  <small style="color: var(--text-muted); font-size: 0.82rem;">
                    📍 <?= e($ev['venue']) ?> &bull; 📅 <?= date('M d, Y', strtotime($ev['event_date'])) ?>
                  </small>
                </div>
                <span class="badge badge-info"><?= e($ev['category']) ?></span>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>

    </section>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
