<?php
/**
 * Semantic HTML5 Header & Accessible Navigation
 *
 * 10 Pages:
 *  1. Home         2. About       3. Courses    4. Events
 *  5. Contact      6. Feedback    7. Register   8. Login
 *  9. Dashboard   10. Students
 */

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/helpers.php';

$currentUser   = current_user();
$currentScript = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="CHARUSAT Campus Portal – Student &amp; Event Administration, Changa, Gujarat">
  <title><?= isset($pageTitle) ? e($pageTitle) . ' | CHARUSAT Campus Portal' : 'CHARUSAT Campus Portal' ?></title>

  <!-- Core Stylesheets -->
  <link rel="stylesheet" href="<?= base_url('css/main.css') ?>">
  <link rel="stylesheet" href="<?= base_url('css/components.css?v=' . filemtime(__DIR__ . '/../css/components.css')) ?>">
  <link rel="stylesheet" href="<?= base_url('css/responsive.css') ?>">
</head>
<body>
  <!-- Site Header -->
  <header class="site-header" role="banner">
    <div class="container nav-wrapper">

      <!-- Brand: Official CHARUSAT Logo -->
      <a href="<?= base_url('index.php') ?>" class="brand-logo" aria-label="CHARUSAT Campus Portal Homepage"
         style="display: flex; align-items: center; gap: 0.6rem; text-decoration: none;">
        <img
          src="<?= base_url('assets/images/charusat_official_logo.png') ?>"
          alt="CHARUSAT Logo"
          height="48"
          style="object-fit: contain; flex-shrink: 0;"
        >
      </a>

      <!-- Main Navigation — 10 Pages -->
      <nav class="main-nav" role="navigation" aria-label="Primary Navigation">
        <button type="button" class="mobile-nav-toggle" aria-expanded="false" aria-label="Toggle navigation menu">
          &#x2630;
        </button>

        <ul class="nav-menu">
          <!-- Page 1: Home -->
          <li>
            <a href="<?= base_url('index.php') ?>"
               class="nav-link <?= ($currentScript === 'index.php') ? 'active' : '' ?>"
               <?= ($currentScript === 'index.php') ? 'aria-current="page"' : '' ?>>
              Home
            </a>
          </li>

          <!-- Page 2: About -->
          <li>
            <a href="<?= base_url('about.php') ?>"
               class="nav-link <?= ($currentScript === 'about.php') ? 'active' : '' ?>"
               <?= ($currentScript === 'about.php') ? 'aria-current="page"' : '' ?>>
              About
            </a>
          </li>

          <!-- Page 3: Courses -->
          <li>
            <a href="<?= base_url('explore.php') ?>"
               class="nav-link <?= ($currentScript === 'explore.php') ? 'active' : '' ?>"
               <?= ($currentScript === 'explore.php') ? 'aria-current="page"' : '' ?>>
              Courses
            </a>
          </li>

          <!-- Page 4: Events -->
          <li>
            <a href="<?= base_url('events/index.php') ?>"
               class="nav-link <?= (strpos($_SERVER['PHP_SELF'], '/events/') !== false) ? 'active' : '' ?>"
               <?= (strpos($_SERVER['PHP_SELF'], '/events/') !== false) ? 'aria-current="page"' : '' ?>>
              Events
            </a>
          </li>

          <!-- Page 5: Contact -->
          <li>
            <a href="<?= base_url('contact.php') ?>"
               class="nav-link <?= ($currentScript === 'contact.php') ? 'active' : '' ?>"
               <?= ($currentScript === 'contact.php') ? 'aria-current="page"' : '' ?>>
              Contact
            </a>
          </li>

          <!-- Page 6: Feedback -->
          <li>
            <a href="<?= base_url('feedback.php') ?>"
               class="nav-link <?= ($currentScript === 'feedback.php') ? 'active' : '' ?>"
               <?= ($currentScript === 'feedback.php') ? 'aria-current="page"' : '' ?>>
              Feedback
            </a>
          </li>

          <?php if (is_logged_in()): ?>
            <!-- Page 9: Dashboard -->
            <li>
              <a href="<?= base_url('dashboard.php') ?>"
                 class="nav-link <?= ($currentScript === 'dashboard.php') ? 'active' : '' ?>"
                 <?= ($currentScript === 'dashboard.php') ? 'aria-current="page"' : '' ?>>
                Dashboard
              </a>
            </li>

            <?php if (has_role('admin')): ?>
              <!-- Page 10: Students -->
              <li>
                <a href="<?= base_url('students/index.php') ?>"
                   class="nav-link <?= (strpos($_SERVER['PHP_SELF'], '/students/') !== false) ? 'active' : '' ?>">
                  Students
                </a>
              </li>
            <?php endif; ?>

          <?php else: ?>
            <!-- Page 7: Register -->
            <li>
              <a href="<?= base_url('register.php') ?>"
                 class="nav-link <?= ($currentScript === 'register.php') ? 'active' : '' ?>"
                 <?= ($currentScript === 'register.php') ? 'aria-current="page"' : '' ?>>
                Register
              </a>
            </li>
            <!-- Page 8: Login -->
            <li>
              <a href="<?= base_url('login.php') ?>"
                 class="nav-link <?= ($currentScript === 'login.php') ? 'active' : '' ?>"
                 <?= ($currentScript === 'login.php') ? 'aria-current="page"' : '' ?>>
                Login
              </a>
            </li>
          <?php endif; ?>
        </ul>
      </nav>

      <!-- Auth State -->
      <div class="nav-actions">
        <?php if (is_logged_in()): ?>
          <div style="display: flex; align-items: center; gap: 0.75rem;">
            <span class="badge <?= has_role('admin') ? 'badge-primary' : 'badge-info' ?>" style="font-size: 0.8rem;">
              <?= e($currentUser['name']) ?> (<?= ucfirst(e($currentUser['role'])) ?>)
            </span>
            <a href="<?= base_url('logout.php') ?>" class="btn btn-outline btn-sm">
              Sign Out
            </a>
          </div>
        <?php else: ?>
          <a href="<?= base_url('login.php') ?>" class="btn btn-outline btn-sm">Sign In</a>
          <a href="<?= base_url('register.php') ?>" class="btn btn-primary btn-sm">Register</a>
        <?php endif; ?>
      </div>
    </div>
  </header>

  <!-- Notification Toast Anchor -->
  <div id="toast-container" aria-live="polite"></div>

  <!-- Flash Messages -->
  <?php $flash = get_flash(); if ($flash): ?>
    <div class="container" style="margin-top: 1.5rem;">
      <div class="alert alert-<?= e($flash['type']) ?>" role="alert">
        <span><?= e($flash['message']) ?></span>
        <button type="button" class="btn-close" onclick="this.parentElement.remove()" aria-label="Close message">&times;</button>
      </div>
    </div>
  <?php endif; ?>

  <!-- Main Content -->
  <main id="main-content" class="main-content" role="main">
