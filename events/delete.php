<?php
/**
 * Event Management - Delete Event
 * Item 12: Delete Event with Safe Media Removal
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

            // Fetch poster path to unlink safely
            $find = $pdo->prepare("SELECT poster_url, title FROM events WHERE id = :id LIMIT 1");
            $find->execute([':id' => $id]);
            $ev = $find->fetch();

            if ($ev) {
                // Delete physical poster file if inside uploads/posters/
                if (!empty($ev['poster_url']) && strpos($ev['poster_url'], 'uploads/posters/') !== false) {
                    $filePath = __DIR__ . '/../' . $ev['poster_url'];
                    if (file_exists($filePath)) {
                        @unlink($filePath);
                    }
                }

                $del = $pdo->prepare("DELETE FROM events WHERE id = :id");
                $del->execute([':id' => $id]);

                log_activity(current_user()['id'], 'DELETE_EVENT', "Deleted event #$id ({$ev['title']})");
                set_flash('success', "Event '{$ev['title']}' was deleted successfully.");
            } else {
                set_flash('warning', 'Event not found.');
            }
        } catch (Exception $e) {
            set_flash('danger', 'Error deleting event: ' . $e->getMessage());
        }
    }
}

header('Location: ' . base_url('events/index.php'));
exit;
