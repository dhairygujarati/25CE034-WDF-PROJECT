<?php
/**
 * Campus Course Explorer
 * Item 6: Rendering External JSON Data using Fetch API, Search and Filter
 */

$pageTitle = 'Explore Courses (Fetch API & JSON)';
$extraScript = 'js/explore.js';
require_once __DIR__ . '/includes/header.php';
?>

<div class="container">
  <div style="margin-bottom: 2rem;">
    <h1 style="margin-top: 0.5rem;">Academic Courses</h1>
  </div>

  <!-- Interactive Search and Filter Toolbar (Flexbox / CSS3) -->
  <div class="toolbar-controls" role="search" aria-label="Course Catalog Filter">
    <div class="search-input-wrapper">
      <span class="search-icon" aria-hidden="true">&#x1F50D;</span>
      <input 
        type="search" 
        id="course-search" 
        class="form-control" 
        placeholder="Search courses by title, course code, department..." 
        aria-label="Search courses by keyword"
      >
    </div>

    <div class="toolbar-filters">
      <div>
        <label for="dept-filter" class="sr-only">Filter by Department</label>
        <select id="dept-filter" class="form-control" style="min-width: 220px;" aria-label="Filter by department">
          <option value="">All Academic Departments</option>
          <option value="Computer Science">Computer Science</option>
          <option value="Data Science">Data Science</option>
          <option value="Information Technology">Information Technology</option>
          <option value="Electronics Engineering">Electronics Engineering</option>
          <option value="Mechanical Engineering">Mechanical Engineering</option>
        </select>
      </div>

      <span id="courses-count" style="font-size: 0.88rem; font-weight: 700; color: var(--text-muted); padding: 0.5rem 0.75rem; background: var(--bg-alt); border-radius: var(--radius-sm);">
        Loading courses...
      </span>
    </div>
  </div>

  <!-- Dynamic Courses Card Grid (CSS Grid Container - Populated by js/explore.js) -->
  <section id="courses-list" class="courses-grid" aria-live="polite" aria-label="Course Offerings Grid">
    <!-- Dynamic course cards inserted via Fetch API -->
  </section>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
