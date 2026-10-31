<?php
/**
 * Student Management - Delete Student
 * Item 11: CSRF-Protected Deletion with MySQL Prepared Statement
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/csrf.php';

require_role('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();

    $id = (int)($_POST['id'] ?? 0);

    if ($id > 0) {
        try {
            $pdo = Database::getConnection();
            $del = $pdo->prepare("DELETE FROM students WHERE id = :id");
            $del->execute([':id' => $id]);

            if ($del->rowCount() > 0) {
                log_activity(current_user()['id'], 'DELETE_STUDENT', "Permanently deleted student #$id");
                set_flash('success', "Student record #{$id} has been permanently deleted.");
            } else {
                set_flash('warning', "Student #{$id} was not found or already removed.");
            }
        } catch (Exception $e) {
            set_flash('danger', 'Deletion error: ' . $e->getMessage());
        }
    }
}

header('Location: ' . base_url('students/index.php'));
exit;
