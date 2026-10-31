<?php
/**
 * Interactive Asynchronous CRUD Studio
 * Item 13: PHP JSON API and AJAX/Fetch-Based CRUD without Page Reload
 */

$pageTitle = 'Asynchronous AJAX CRUD Studio';
$extraScript = 'js/ajax_crud.js';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';

require_role('admin');

require_once __DIR__ . '/includes/header.php';
?>

<div class="container">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
    <div>
      <span class="badge badge-primary">Asynchronous Management</span>
      <h1 style="margin-top: 0.4rem;">AJAX / Fetch-Based CRUD Studio</h1>
      <p style="color: var(--text-muted); font-size: 0.95rem;">
        Perform live Create, Read, Update, and Delete operations without a single browser page reload.
      </p>
    </div>

    <div style="display: flex; gap: 0.75rem;">
      <button type="button" class="btn btn-primary" id="btn-open-add-student">
        &#x2B; Add Student (AJAX Modal)
      </button>
      
    </div>
  </div>

  <!-- AJAX Search and Filter Controls -->
  <div class="toolbar-controls" role="search">
    <div class="search-input-wrapper">
      <span class="search-icon" aria-hidden="true">&#x1F50D;</span>
      <input 
        type="search" 
        id="ajax-search" 
        class="form-control" 
        placeholder="Live filter by name, roll no, email..." 
        aria-label="Filter students via AJAX"
      >
    </div>

    <div class="toolbar-filters">
      <select id="ajax-dept-filter" class="form-control" aria-label="Filter by department via AJAX">
        <option value="">All Academic Departments</option>
        <option value="Computer Science">Computer Science</option>
        <option value="Information Technology">Information Technology</option>
        <option value="Data Science">Data Science</option>
        <option value="Electronics Engineering">Electronics Engineering</option>
        <option value="Mechanical Engineering">Mechanical Engineering</option>
      </select>

      <span class="badge badge-info" style="padding: 0.5rem 0.75rem;">
        &#x26A1; Real-time Fetch API
      </span>
    </div>
  </div>

  <!-- AJAX Students Table -->
  <div class="card">
    <div class="card-header">
      <span style="font-weight: 700; color: var(--text-main);">
        Live Student Records Table
      </span>
      <small style="color: var(--text-muted);">
        Powered by <code>fetch()</code> &amp; DOM diffing
      </small>
    </div>

    <div class="card-body" style="padding: 0;">
      <div class="table-responsive">
        <table class="table" style="margin-bottom: 0;">
          <thead>
            <tr>
              <th>Roll Number</th>
              <th>Full Name</th>
              <th>Email</th>
              <th>Department</th>
              <th>Semester</th>
              <th>GPA</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="ajax-students-body">
            <!-- Dynamically populated by js/ajax_crud.js via fetch() -->
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Accessible Modal Dialog for Create/Edit Student (Item 4 & 13) -->
<div class="modal-backdrop" id="student-modal" role="dialog" aria-modal="true" aria-labelledby="student-modal-title" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-header">
      <h2 id="student-modal-title" class="modal-title">Student Information</h2>
      <button type="button" class="btn-close" data-modal-close aria-label="Close dialog">&times;</button>
    </div>

    <form id="student-modal-form" novalidate>
      <input type="hidden" id="modal_student_id" value="">

      <div class="modal-body">
        <div class="form-row-2">
          <div class="form-group">
            <label for="modal_roll_number" class="form-label">Roll Number <span class="required">*</span></label>
            <input type="text" id="modal_roll_number" class="form-control" placeholder="CS-2026-099" required>
          </div>

          <div class="form-group">
            <label for="modal_email" class="form-label">Email Address <span class="required">*</span></label>
            <input type="email" id="modal_email" class="form-control" placeholder="student@campus.edu" required>
          </div>
        </div>

        <div class="form-row-2">
          <div class="form-group">
            <label for="modal_first_name" class="form-label">First Name <span class="required">*</span></label>
            <input type="text" id="modal_first_name" class="form-control" required>
          </div>

          <div class="form-group">
            <label for="modal_last_name" class="form-label">Last Name <span class="required">*</span></label>
            <input type="text" id="modal_last_name" class="form-control" required>
          </div>
        </div>

        <div class="form-row-2">
          <div class="form-group">
            <label for="modal_phone" class="form-label">Phone Number <span class="required">*</span></label>
            <input type="tel" id="modal_phone" class="form-control" placeholder="+1-555-0199" required>
          </div>

          <div class="form-group">
            <label for="modal_department" class="form-label">Department <span class="required">*</span></label>
            <select id="modal_department" class="form-control" required>
              <option value="Computer Science">Computer Science</option>
              <option value="Information Technology">Information Technology</option>
              <option value="Data Science">Data Science</option>
              <option value="Electronics Engineering">Electronics Engineering</option>
              <option value="Mechanical Engineering">Mechanical Engineering</option>
            </select>
          </div>
        </div>

        <div class="form-row-3">
          <div class="form-group">
            <label for="modal_semester" class="form-label">Semester</label>
            <input type="number" id="modal_semester" min="1" max="10" value="1" class="form-control" required>
          </div>

          <div class="form-group">
            <label for="modal_gpa" class="form-label">GPA</label>
            <input type="number" id="modal_gpa" step="0.01" min="0" max="4.00" value="3.50" class="form-control">
          </div>

          <div class="form-group">
            <label for="modal_status" class="form-label">Status</label>
            <select id="modal_status" class="form-control">
              <option value="Active">Active</option>
              <option value="Inactive">Inactive</option>
              <option value="Graduated">Graduated</option>
              <option value="Suspended">Suspended</option>
            </select>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-outline" data-modal-close>Cancel</button>
        <button type="submit" class="btn btn-primary">&#x2713; Save via Fetch API</button>
      </div>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
