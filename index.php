<?php
/**
 * Campus Portal - Home Page
 */

$pageTitle = 'Home';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/config/database.php';

// Fetch quick statistics from database
$stats = ['students' => 0, 'events' => 0, 'departments' => 6];
try {
    $pdo = Database::getConnection();
    $stats['students'] = (int)$pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
    $stats['events']   = (int)$pdo->query("SELECT COUNT(*) FROM events")->fetchColumn();
} catch (Exception $e) {
    // Graceful fallback
}
?>

<div class="container">
  <!-- Hero Section with Official CHARUSAT Campus Gate Background -->
  <section class="card" style="
    margin-bottom: 2.5rem;
    position: relative;
    border-radius: var(--radius-lg);
    overflow: hidden;
    background-image: linear-gradient(rgba(15, 34, 71, 0.78), rgba(15, 34, 71, 0.85)), url('<?= base_url('assets/images/charusat_campus_bg.jpg') ?>');
    background-size: cover;
    background-position: center;
    border: none;
    box-shadow: var(--shadow-md);
    padding: 3.5rem 2.5rem;
    color: #ffffff;
  ">
    <div style="max-width: 780px;">
      <span style="
        display: inline-block;
        background: #f5c518;
        color: #0f2247;
        font-size: 0.8rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        border-radius: 4px;
        padding: 0.25rem 0.65rem;
        margin-bottom: 1rem;
        text-transform: uppercase;
      ">CHARUSAT University · Changa, Gujarat</span>
      <h1 style="font-size: 2.5rem; line-height: 1.2; margin-bottom: 1rem; color: #ffffff;">
        CHARUSAT Student Portal
      </h1>
      <p style="font-size: 1.1rem; line-height: 1.6; color: #e2e8f0; margin-bottom: 1.75rem;">
        Your one-stop digital gateway for academic resources, campus events, course exploration, and student services at Charotar University of Science &amp; Technology.
      </p>

      <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <?php if (!is_logged_in()): ?>
          <a href="<?= base_url('register.php') ?>" class="btn btn-primary" style="background: #f5c518; color: #0f2247; font-weight: 700; border: none;">
            ✍ Student Registration
          </a>
          <a href="<?= base_url('login.php') ?>" class="btn" style="background: rgba(255,255,255,0.15); color: #ffffff; border: 1px solid rgba(255,255,255,0.4); backdrop-filter: blur(4px);">
            🔑 Sign In
          </a>
        <?php else: ?>
          <a href="<?= base_url('dashboard.php') ?>" class="btn btn-primary" style="background: #f5c518; color: #0f2247; font-weight: 700; border: none;">
            📊 Open Dashboard
          </a>
          <a href="<?= base_url('events/index.php') ?>" class="btn" style="background: rgba(255,255,255,0.15); color: #ffffff; border: 1px solid rgba(255,255,255,0.4); backdrop-filter: blur(4px);">
            📅 Campus Events
          </a>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <!-- Key Statistics Grid -->
  <section aria-labelledby="stats-heading" style="margin-bottom: 3rem;">
    <h2 id="stats-heading" class="sr-only">CHARUSAT Campus Statistics</h2>
    <div class="metrics-grid">
      <div class="metric-card">
        <div class="metric-icon" style="background: #e0f2fe; color: #0284c7;">&#x1F468;&#x200D;&#x1F393;</div>
        <div class="metric-data">
          <div class="metric-value"><?= $stats['students'] ?></div>
          <div class="metric-label">Enrolled Students</div>
        </div>
      </div>

      <div class="metric-card">
        <div class="metric-icon" style="background: #fef3c7; color: #d97706;">&#x1F3A8;</div>
        <div class="metric-data">
          <div class="metric-value"><?= $stats['events'] ?></div>
          <div class="metric-label">Active Campus Events</div>
        </div>
      </div>

      <div class="metric-card">
        <div class="metric-icon" style="background: #dcfce7; color: #16a34a;">&#x1F3DB;&#xFE0F;</div>
        <div class="metric-data">
          <div class="metric-value">6</div>
          <div class="metric-label">Academic Departments</div>
        </div>
      </div>

      <div class="metric-card">
        <div class="metric-icon" style="background: #f3e8ff; color: #9333ea;">&#x1F393;</div>
        <div class="metric-data">
          <div class="metric-value">70+</div>
          <div class="metric-label">Programs Offered</div>
        </div>
      </div>
    </div>
  </section>

  <!-- Quick Links / Portal Features -->
  <section aria-labelledby="features-heading" style="margin-bottom: 3rem;">
    <div style="text-align: center; max-width: 650px; margin: 0 auto 2rem;">
      <h2 id="features-heading">Explore the Portal</h2>
      <p style="color: var(--text-muted);">
        Access all student services and academic resources in one place.
      </p>
    </div>

    <div class="courses-grid">
      <!-- Feature 1: About CHARUSAT -->
      <article class="card">
        <div class="card-body">
          <span class="badge badge-primary" style="margin-bottom: 0.75rem;">University</span>
          <h3>About CHARUSAT</h3>
          <p style="font-size: 0.92rem; color: var(--text-muted); line-height: 1.5;">
            Discover Charotar University of Science &amp; Technology, our NAAC A+ accredited institutes, campus history, and vision.
          </p>
        </div>
        <div class="card-footer">
          <a href="<?= base_url('about.php') ?>" style="font-weight: 600; font-size: 0.88rem;">About University &rarr;</a>
        </div>
      </article>

      <!-- Feature 2 -->
      <article class="card">
        <div class="card-body">
          <span class="badge badge-info" style="margin-bottom: 0.75rem;">Academics</span>
          <h3>Academic Courses</h3>
          <p style="font-size: 0.92rem; color: var(--text-muted); line-height: 1.5;">
            Browse all available academic courses across departments including Computer Science, IT, Mechanical, Electrical, and more.
          </p>
        </div>
        <div class="card-footer">
          <a href="<?= base_url('explore.php') ?>" style="font-weight: 600; font-size: 0.88rem;">View Courses &rarr;</a>
        </div>
      </article>

      <!-- Feature 3 -->
      <article class="card">
        <div class="card-body">
          <span class="badge badge-primary" style="margin-bottom: 0.75rem;">Events</span>
          <h3>Campus Events</h3>
          <p style="font-size: 0.92rem; color: var(--text-muted); line-height: 1.5;">
            Stay updated with upcoming seminars, hackathons, cultural fests, and workshops happening across the CHARUSAT campus.
          </p>
        </div>
        <div class="card-footer">
          <a href="<?= base_url('events/index.php') ?>" style="font-weight: 600; font-size: 0.88rem;">View Events &rarr;</a>
        </div>
      </article>

      <!-- Feature 4 -->
      <article class="card">
        <div class="card-body">
          <span class="badge badge-success" style="margin-bottom: 0.75rem;">Directory</span>
          <h3>Contact Directory</h3>
          <p style="font-size: 0.92rem; color: var(--text-muted); line-height: 1.5;">
            Official campus numbers, emails, office timings, registrar desk, and department contact info.
          </p>
        </div>
        <div class="card-footer">
          <a href="<?= base_url('contact.php') ?>" style="font-weight: 600; font-size: 0.88rem;">Contact Us &rarr;</a>
        </div>
      </article>

      <!-- Feature 5 -->
      <article class="card">
        <div class="card-body">
          <span class="badge badge-warning" style="margin-bottom: 0.75rem;">Support</span>
          <h3>Student Feedback</h3>
          <p style="font-size: 0.92rem; color: var(--text-muted); line-height: 1.5;">
            Share your academic feedback, facilities review, and queries directly with the administration.
          </p>
        </div>
        <div class="card-footer">
          <a href="<?= base_url('feedback.php') ?>" style="font-weight: 600; font-size: 0.88rem;">Submit Feedback &rarr;</a>
        </div>
      </article>
    </div>
  </section>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
