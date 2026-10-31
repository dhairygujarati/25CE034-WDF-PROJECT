<?php
/**
 * Student Management - List, Search, Filter & Pagination
 * Item 11: Student Management CRUD with Search, Filter, and Prepared MySQL Statements
 */

$pageTitle = 'Student Records Management';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/csrf.php';

require_role('admin'); // Only admins can access traditional management CRUD

$pdo = Database::getConnection();

// Search and filter parameters
$search     = trim($_GET['search'] ?? '');
$department = trim($_GET['department'] ?? '');
$status     = trim($_GET['status'] ?? '');
$page       = max(1, (int)($_GET['page'] ?? 1));
$perPage    = 6;
$offset     = ($page - 1) * $perPage;

// Base query builder with PDO prepared statements
$whereClauses = ['1=1'];
$params = [];

if ($search !== '') {
    $whereClauses[] = "(first_name LIKE :s1 OR last_name LIKE :s2 OR roll_number LIKE :s3 OR email LIKE :s4)";
    $wildcard = "%{$search}%";
    $params[':s1'] = $wildcard;
    $params[':s2'] = $wildcard;
    $params[':s3'] = $wildcard;
    $params[':s4'] = $wildcard;
}

if ($department !== '') {
    $whereClauses[] = "department = :dept";
    $params[':dept'] = $department;
}

if ($status !== '') {
    $whereClauses[] = "status = :status";
    $params[':status'] = $status;
}

$whereSql = implode(' AND ', $whereClauses);

// 1. Total records count for pagination
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM students WHERE {$whereSql}");
$countStmt->execute($params);
$totalRecords = (int)$countStmt->fetchColumn();
$totalPages = max(1, ceil($totalRecords / $perPage));

// 2. Query paginated records
$querySql = "SELECT * FROM students WHERE {$whereSql} ORDER BY created_at DESC LIMIT {$perPage} OFFSET {$offset}";
$dataStmt = $pdo->prepare($querySql);
$dataStmt->execute($params);
$students = $dataStmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
    <div>
      <span class="badge badge-primary">Student Records</span>
      <h1 style="margin-top: 0.4rem;">Student Administration Directory</h1>
      <p style="color: var(--text-muted); font-size: 0.95rem;">
        Full CRUD operations powered by strict MySQL Prepared Statements preventing SQL injection vulnerabilities.
      </p>
    </div>

    <div style="display: flex; gap: 0.75rem;">
      <a href="<?= base_url('students/create.php') ?>" class="btn btn-primary">
        &#x2B; Add New Student
      </a>
      
    </div>
  </div>

  <!-- Search & Filter Controls Toolbar -->
  <form method="GET" action="<?= base_url('students/index.php') ?>" class="toolbar-controls" role="search">
    <div class="search-input-wrapper">
      <span class="search-icon" aria-hidden="true">&#x1F50D;</span>
      <input 
        type="search" 
        name="search" 
        class="form-control" 
        placeholder="Search by name, roll no, email..." 
        value="<?= e($search) ?>"
        aria-label="Search students"
      >
    </div>

    <div class="toolbar-filters">
      <select name="department" class="form-control" aria-label="Filter by Department">
        <option value="">All Departments</option>
        <option value="Computer Science" <?= ($department === 'Computer Science') ? 'selected' : '' ?>>Computer Science</option>
        <option value="Information Technology" <?= ($department === 'Information Technology') ? 'selected' : '' ?>>Information Technology</option>
        <option value="Data Science" <?= ($department === 'Data Science') ? 'selected' : '' ?>>Data Science</option>
        <option value="Electronics Engineering" <?= ($department === 'Electronics Engineering') ? 'selected' : '' ?>>Electronics Engineering</option>
        <option value="Mechanical Engineering" <?= ($department === 'Mechanical Engineering') ? 'selected' : '' ?>>Mechanical Engineering</option>
      </select>

      <select name="status" class="form-control" aria-label="Filter by Status">
        <option value="">All Statuses</option>
        <option value="Active" <?= ($status === 'Active') ? 'selected' : '' ?>>Active</option>
        <option value="Inactive" <?= ($status === 'Inactive') ? 'selected' : '' ?>>Inactive</option>
        <option value="Graduated" <?= ($status === 'Graduated') ? 'selected' : '' ?>>Graduated</option>
        <option value="Suspended" <?= ($status === 'Suspended') ? 'selected' : '' ?>>Suspended</option>
      </select>

      <button type="submit" class="btn btn-primary btn-sm">Filter</button>
      <?php if ($search !== '' || $department !== '' || $status !== ''): ?>
        <a href="<?= base_url('students/index.php') ?>" class="btn btn-outline btn-sm">Reset</a>
      <?php endif; ?>
    </div>
  </form>

  <!-- Students Data Table -->
  <div class="card">
    <div class="card-header">
      <span style="font-weight: 700; color: var(--text-main);">
        Showing <?= count($students) ?> of <?= $totalRecords ?> Student Records
      </span>
      <span style="font-size: 0.85rem; color: var(--text-muted);">
        Page <?= $page ?> of <?= $totalPages ?>
      </span>
    </div>

    <div class="card-body" style="padding: 0;">
      <div class="table-responsive">
        <table class="table" style="margin-bottom: 0;">
          <thead>
            <tr>
              <th>Roll Number</th>
              <th>Full Name</th>
              <th>Email</th>
              <th>Phone</th>
              <th>Department</th>
              <th>Semester</th>
              <th>GPA</th>
              <th>Status</th>
              <th style="text-align: right;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($students)): ?>
              <tr>
                <td colspan="9" style="text-align: center; padding: 3rem; color: var(--text-muted);">
                  No students matched your search criteria.
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($students as $s): ?>
                <tr>
                  <td><strong><?= e($s['roll_number']) ?></strong></td>
                  <td><?= e($s['first_name'] . ' ' . $s['last_name']) ?></td>
                  <td><?= e($s['email']) ?></td>
                  <td><?= e($s['phone']) ?></td>
                  <td><?= e($s['department']) ?></td>
                  <td>Sem <?= (int)$s['semester'] ?></td>
                  <td><strong><?= number_format((float)$s['gpa'], 2) ?></strong></td>
                  <td>
                    <?php
                      $badgeClass = 'badge-primary';
                      if ($s['status'] === 'Active') $badgeClass = 'badge-success';
                      if ($s['status'] === 'Graduated') $badgeClass = 'badge-info';
                      if ($s['status'] === 'Inactive') $badgeClass = 'badge-warning';
                      if ($s['status'] === 'Suspended') $badgeClass = 'badge-danger';
                    ?>
                    <span class="badge <?= $badgeClass ?>"><?= e($s['status']) ?></span>
                  </td>
                  <td style="text-align: right; white-space: nowrap;">
                    <a href="<?= base_url('students/edit.php?id=' . $s['id']) ?>" class="btn btn-outline btn-sm">
                      Edit
                    </a>
                    <form method="POST" action="<?= base_url('students/delete.php') ?>" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to permanently delete this student record?');">
                      <?= csrf_field() ?>
                      <input type="hidden" name="id" value="<?= $s['id'] ?>">
                      <button type="submit" class="btn btn-danger btn-sm">
                        Delete
                      </button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Pagination Footer -->
    <?php if ($totalPages > 1): ?>
      <div class="card-footer" style="display: flex; justify-content: center; gap: 0.5rem;">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
          <a 
            href="<?= base_url('students/index.php?page=' . $i . '&search=' . urlencode($search) . '&department=' . urlencode($department) . '&status=' . urlencode($status)) ?>" 
            class="btn btn-sm <?= ($i === $page) ? 'btn-primary' : 'btn-outline' ?>"
          >
            <?= $i ?>
          </a>
        <?php endfor; ?>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
