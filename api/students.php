<?php
/**
 * =====================================================================
 * RESTful JSON API - STUDENTS RESOURCE
 * Item 13: PHP JSON API and AJAX/Fetch-Based CRUD without Page Reload
 * =====================================================================
 */

header('Content-Type: application/json; charset=UTF-8');
header('X-Content-Type-Options: nosniff');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    $pdo = Database::getConnection();

    switch ($method) {
        // -------------------------------------------------------------
        // GET: List, Search, and Filter Students
        // -------------------------------------------------------------

        case 'GET':
            $search = trim($_GET['search'] ?? '');
            $department = trim($_GET['department'] ?? '');
            $status = trim($_GET['status'] ?? '');

            $sql = "SELECT id, roll_number, first_name, last_name, email, phone, department, semester, gpa, status, created_at FROM students WHERE 1=1";
            $params = [];

            if ($search !== '') {
                $sql .= " AND (first_name LIKE :s1 OR last_name LIKE :s2 OR roll_number LIKE :s3 OR email LIKE :s4)";
                $wildcard = "%{$search}%";
                $params[':s1'] = $wildcard;
                $params[':s2'] = $wildcard;
                $params[':s3'] = $wildcard;
                $params[':s4'] = $wildcard;
            }

            if ($department !== '') {
                $sql .= " AND department = :dept";
                $params[':dept'] = $department;
            }

            if ($status !== '') {
                $sql .= " AND status = :status";
                $params[':status'] = $status;
            }

            $sql .= " ORDER BY created_at DESC LIMIT 100";

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $students = $stmt->fetchAll();

            echo json_encode([
                'success' => true,
                'count'   => count($students),
                'data'    => $students
            ], JSON_UNESCAPED_UNICODE);
            break;

        // -------------------------------------------------------------
        // POST: Create New Student
        // -------------------------------------------------------------
        case 'POST':
            if (!has_role('admin')) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Unauthorized: Admin privileges required.']);
                exit;
            }

            $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

            $rollNumber = trim($input['roll_number'] ?? '');
            $firstName  = trim($input['first_name'] ?? '');
            $lastName   = trim($input['last_name'] ?? '');
            $email      = trim($input['email'] ?? '');
            $phone      = trim($input['phone'] ?? '');
            $department = trim($input['department'] ?? '');
            $semester   = (int)($input['semester'] ?? 1);
            $gpa        = (float)($input['gpa'] ?? 0.00);
            $status     = trim($input['status'] ?? 'Active');

            // Server-Side Validations
            $errors = [];
            if ($rollNumber === '') $errors[] = 'Roll number is required.';
            if ($firstName === '')  $errors[] = 'First name is required.';
            if ($lastName === '')   $errors[] = 'Last name is required.';
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
            if ($phone === '')      $errors[] = 'Phone number is required.';
            if ($department === '') $errors[] = 'Department is required.';

            // Duplicate Checks
            $dupCheck = $pdo->prepare("SELECT id FROM students WHERE roll_number = :roll OR email = :email LIMIT 1");
            $dupCheck->execute([':roll' => $rollNumber, ':email' => $email]);
            if ($dupCheck->fetch()) {
                $errors[] = 'A student with this Roll Number or Email already exists.';
            }

            if (!empty($errors)) {
                http_response_code(422);
                echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
                exit;
            }

            $insert = $pdo->prepare("
                INSERT INTO students (roll_number, first_name, last_name, email, phone, department, semester, gpa, status)
                VALUES (:roll, :fname, :lname, :email, :phone, :dept, :sem, :gpa, :status)
            ");
            $insert->execute([
                ':roll'   => $rollNumber,
                ':fname'  => $firstName,
                ':lname'  => $lastName,
                ':email'  => $email,
                ':phone'  => $phone,
                ':dept'   => $department,
                ':sem'    => $semester,
                ':gpa'    => $gpa,
                ':status' => $status
            ]);

            $newId = (int)$pdo->lastInsertId();
            log_activity(current_user()['id'] ?? null, 'CREATE_STUDENT', "Added student ID: $newId ($rollNumber)");

            http_response_code(201);
            echo json_encode([
                'success' => true,
                'message' => 'Student record created successfully.',
                'data'    => ['id' => $newId, 'roll_number' => $rollNumber]
            ]);
            break;

        // -------------------------------------------------------------
        // PUT: Update Student Record
        // -------------------------------------------------------------
        case 'PUT':
            if (!has_role('admin')) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Unauthorized: Admin privileges required.']);
                exit;
            }

            $input = json_decode(file_get_contents('php://input'), true);
            $id = (int)($input['id'] ?? $_GET['id'] ?? 0);

            if ($id <= 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid or missing student ID.']);
                exit;
            }

            $rollNumber = trim($input['roll_number'] ?? '');
            $firstName  = trim($input['first_name'] ?? '');
            $lastName   = trim($input['last_name'] ?? '');
            $email      = trim($input['email'] ?? '');
            $phone      = trim($input['phone'] ?? '');
            $department = trim($input['department'] ?? '');
            $semester   = (int)($input['semester'] ?? 1);
            $gpa        = (float)($input['gpa'] ?? 0.00);
            $status     = trim($input['status'] ?? 'Active');

            // Duplicate checks excluding current student ID
            $dupCheck = $pdo->prepare("SELECT id FROM students WHERE (roll_number = :roll OR email = :email) AND id != :id LIMIT 1");
            $dupCheck->execute([':roll' => $rollNumber, ':email' => $email, ':id' => $id]);
            if ($dupCheck->fetch()) {
                http_response_code(422);
                echo json_encode(['success' => false, 'message' => 'Roll number or email already belongs to another student.']);
                exit;
            }

            $update = $pdo->prepare("
                UPDATE students SET 
                    roll_number = :roll,
                    first_name = :fname,
                    last_name = :lname,
                    email = :email,
                    phone = :phone,
                    department = :dept,
                    semester = :sem,
                    gpa = :gpa,
                    status = :status
                WHERE id = :id
            ");
            $update->execute([
                ':roll'   => $rollNumber,
                ':fname'  => $firstName,
                ':lname'  => $lastName,
                ':email'  => $email,
                ':phone'  => $phone,
                ':dept'   => $department,
                ':sem'    => $semester,
                ':gpa'    => $gpa,
                ':status' => $status,
                ':id'     => $id
            ]);

            log_activity(current_user()['id'] ?? null, 'UPDATE_STUDENT', "Updated student ID: $id ($rollNumber)");

            echo json_encode(['success' => true, 'message' => 'Student record updated successfully.']);
            break;

        // -------------------------------------------------------------
        // DELETE: Remove Student Record
        // -------------------------------------------------------------
        case 'DELETE':
            if (!has_role('admin')) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Unauthorized: Admin privileges required.']);
                exit;
            }

            $id = (int)($_GET['id'] ?? 0);
            if ($id <= 0) {
                // Try reading from JSON body
                $input = json_decode(file_get_contents('php://input'), true);
                $id = (int)($input['id'] ?? 0);
            }

            if ($id <= 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Valid student ID is required for deletion.']);
                exit;
            }

            $del = $pdo->prepare("DELETE FROM students WHERE id = :id");
            $del->execute([':id' => $id]);

            if ($del->rowCount() > 0) {
                log_activity(current_user()['id'] ?? null, 'DELETE_STUDENT', "Deleted student ID: $id");
                echo json_encode(['success' => true, 'message' => 'Student record permanently removed.']);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Student not found.']);
            }
            break;

        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'HTTP Method Not Allowed']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
