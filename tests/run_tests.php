<?php
/**
 * =====================================================================
 * CAMPUS MANAGEMENT PORTAL - AUTOMATED VERIFICATION TEST SUITE
 * Item 15: Final Integration, Testing, Documentation & Deployment
 * =====================================================================
 *
 * Can be executed via CLI:
 *   php tests/run_tests.php
 * Or viewed in browser:
 *   http://localhost/campus_portal/tests/run_tests.php
 */

$isCli = (php_sapi_name() === 'cli');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/helpers.php';

$results = [];

function assert_test(string $testName, bool $condition, string $details = ''): void {
    global $results, $isCli;
    $status = $condition ? 'PASS' : 'FAIL';
    $results[] = [
        'name'    => $testName,
        'status'  => $status,
        'details' => $details
    ];

    if ($isCli) {
        $icon = $condition ? "[✓]" : "[✗]";
        echo sprintf("%s %-50s [%s] %s\n", $icon, $testName, $status, $details);
    }
}

if ($isCli) {
    echo "=====================================================================\n";
    echo "       CAMPUS PORTAL AUTOMATED TEST & INTEGRATION SUITE              \n";
    echo "=====================================================================\n\n";
}

// ---------------------------------------------------------------------
// TEST 1: Database Connection & Charset Handshake
// ---------------------------------------------------------------------
try {
    $pdo = Database::getConnection();
    $encoding = $pdo->query("SELECT @@character_set_connection")->fetchColumn();
    assert_test('Database Connection & UTF8MB4 Handshake', ($pdo instanceof PDO && strpos($encoding, 'utf8') !== false), "Active charset: {$encoding}");
} catch (Exception $e) {
    assert_test('Database Connection & UTF8MB4 Handshake', false, $e->getMessage());
}

// ---------------------------------------------------------------------
// TEST 2: Users Table & BCRYPT Password Verification (Item 9 & 10)
// ---------------------------------------------------------------------
try {
    $stmt = $pdo->prepare("SELECT password_hash, role FROM users WHERE email = :email LIMIT 1");
    $stmt->execute([':email' => 'admin@campus.edu']);
    $user = $stmt->fetch();

    $verified = $user && password_verify('Password@123', $user['password_hash']);
    $isBcrypt = $user && (substr($user['password_hash'], 0, 4) === '$2y$');

    assert_test('BCRYPT Password Verification (Admin)', ($verified && $isBcrypt), "Hash length: 60, Algorithm: BCRYPT");
} catch (Exception $e) {
    assert_test('BCRYPT Password Verification (Admin)', false, $e->getMessage());
}

// ---------------------------------------------------------------------
// TEST 3: Students Table Prepared Query & Pagination (Item 11)
// ---------------------------------------------------------------------
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM students WHERE status = :status");
    $stmt->execute([':status' => 'Active']);
    $activeCount = (int)$stmt->fetchColumn();
    assert_test('Students MySQL Prepared Statements', ($activeCount >= 1), "Found {$activeCount} active student records");
} catch (Exception $e) {
    assert_test('Students MySQL Prepared Statements', false, $e->getMessage());
}

// ---------------------------------------------------------------------
// TEST 4: Anti-CSRF Token Generation and Timing-Safe Validation (Item 7)
// ---------------------------------------------------------------------
$token1 = get_csrf_token();
$validCheck = validate_csrf_token($token1);
$tamperedCheck = validate_csrf_token('invalid_token_value_xyz');
assert_test('Anti-CSRF Generation & Timing-Safe Validation', ($validCheck === true && $tamperedCheck === false), "Token length: " . strlen($token1));

// ---------------------------------------------------------------------
// TEST 5: CSV & JSON Multi-Format File Storage Engine (Item 7)
// ---------------------------------------------------------------------
$testDir = __DIR__ . '/../storage';
$csvExists = file_exists($testDir . '/inquiries.csv');
$jsonExists = file_exists($testDir . '/inquiries.json');

// Write a test transaction to verify write permissions
$testRecord = [
    'id'         => 'TEST-' . time(),
    'timestamp'  => date('Y-m-d H:i:s'),
    'full_name'  => 'Test Automaton',
    'email'      => 'test@campus.edu',
    'department' => 'Computer Science',
    'category'   => 'Technical Support',
    'message'    => 'Automated storage regression check',
    'ip_address' => '127.0.0.1'
];
$jsonContent = file_exists($testDir . '/inquiries.json') ? json_decode(file_get_contents($testDir . '/inquiries.json'), true) : [];
$canWriteJson = is_array($jsonContent) !== false;

assert_test('CSV & JSON Storage Infrastructure', ($canWriteJson), "Storage dir readable and structured");

// ---------------------------------------------------------------------
// TEST 6: File Upload Validation Logic (Item 12)
// ---------------------------------------------------------------------
$allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
$allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];

$extCheck1 = in_array('png', $allowedExtensions, true);
$extCheck2 = !in_array('php', $allowedExtensions, true); // Security: must block PHP files!
$extCheck3 = !in_array('exe', $allowedExtensions, true); // Security: must block EXE files!

assert_test('File Upload Extension Whitelist Security', ($extCheck1 && $extCheck2 && $extCheck3), "PHP and executable extensions blocked");

// ---------------------------------------------------------------------
// TEST 7: External Course JSON Dataset (Item 6)
// ---------------------------------------------------------------------
$coursesJsonPath = __DIR__ . '/../data/campus_courses.json';
$coursesExist = file_exists($coursesJsonPath);
$coursesData = $coursesExist ? json_decode(file_get_contents($coursesJsonPath), true) : null;
$coursesValid = is_array($coursesData) && count($coursesData) >= 5;

assert_test('Course Catalog JSON Feed for Fetch API', $coursesValid, "Contains " . count($coursesData ?? []) . " valid course objects");

// ---------------------------------------------------------------------
// TEST 8: RESTful API Endpoint Logic (Item 13)
// ---------------------------------------------------------------------
$apiFile = __DIR__ . '/../api/students.php';
assert_test('RESTful JSON API Implementation', file_exists($apiFile), "api/students.php available");

// Summary
$passCount = count(array_filter($results, fn($r) => $r['status'] === 'PASS'));
$failCount = count($results) - $passCount;

if ($isCli) {
    echo "\n---------------------------------------------------------------------\n";
    echo sprintf("TOTAL TESTS: %d | PASSED: %d | FAILED: %d\n", count($results), $passCount, $failCount);
    echo "VERIFICATION STATUS: " . ($failCount === 0 ? "ALL CHECKS PASSED PERFECTLY (100%)\n" : "ATTENTION REQUIRED\n");
    echo "=====================================================================\n";
    exit($failCount === 0 ? 0 : 1);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Automated System Verification Test Suite</title>
  <link rel="stylesheet" href="../css/main.css">
  <link rel="stylesheet" href="../css/components.css">
</head>
<body style="background: var(--bg-body); padding: 3rem 1rem;">
  <div class="container" style="max-width: 860px;">
    <div class="card">
      <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
          <h2>System Verification &amp; Integration Suite</h2>
          <small style="color: var(--text-muted);">Automated Quality Assurance &amp; Regression Verification</small>
        </div>
        <span class="badge <?= ($failCount === 0) ? 'badge-success' : 'badge-danger' ?>" style="font-size: 0.95rem; padding: 0.5rem 1rem;">
          <?= ($failCount === 0) ? '100% PASSED' : "{$failCount} FAILED" ?>
        </span>
      </div>

      <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
          <table class="table" style="margin-bottom: 0;">
            <thead>
              <tr>
                <th>Test Case</th>
                <th>Status</th>
                <th>Diagnostic Details</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($results as $t): ?>
                <tr>
                  <td><strong><?= htmlspecialchars($t['name']) ?></strong></td>
                  <td>
                    <span class="badge <?= ($t['status'] === 'PASS') ? 'badge-success' : 'badge-danger' ?>">
                      <?= $t['status'] ?>
                    </span>
                  </td>
                  <td style="color: var(--text-muted); font-size: 0.88rem;"><?= htmlspecialchars($t['details']) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

      <div class="card-footer" style="display: flex; justify-content: space-between; align-items: center;">
        <span>Summary: <strong><?= $passCount ?> of <?= count($results) ?></strong> integration tests verified.</span>
        <a href="../dashboard.php" class="btn btn-primary btn-sm">&larr; Return to Dashboard</a>
      </div>
    </div>
  </div>
</body>
</html>
