<?php


header('Content-Type: application/json; charset=UTF-8');
header('X-Content-Type-Options: nosniff');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    $pdo = Database::getConnection();

    switch ($method) {
        case 'GET':
            $category = trim($_GET['category'] ?? '');
            $sql = "SELECT id, title, category, description, event_date, event_time, venue, organizer, max_participants, poster_url, created_at FROM events WHERE 1=1";
            $params = [];

            if ($category !== '') {
                $sql .= " AND category = :cat";
                $params[':cat'] = $category;
            }

            $sql .= " ORDER BY event_date ASC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $events = $stmt->fetchAll();

            echo json_encode([
                'success' => true,
                'count'   => count($events),
                'data'    => $events
            ], JSON_UNESCAPED_UNICODE);
            break;

        case 'DELETE':
            if (!has_role('admin')) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Admin privileges required.']);
                exit;
            }

            $id = (int)($_GET['id'] ?? 0);
            if ($id <= 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Valid event ID is required.']);
                exit;
            }

            // Find event to safely unlink poster file
            $find = $pdo->prepare("SELECT poster_url FROM events WHERE id = :id");
            $find->execute([':id' => $id]);
            $event = $find->fetch();

            if ($event) {
                if (!empty($event['poster_url']) && file_exists(__DIR__ . '/../' . $event['poster_url']) && strpos($event['poster_url'], 'uploads/posters/') !== false) {
                    @unlink(__DIR__ . '/../' . $event['poster_url']);
                }

                $del = $pdo->prepare("DELETE FROM events WHERE id = :id");
                $del->execute([':id' => $id]);

                log_activity(current_user()['id'] ?? null, 'DELETE_EVENT', "Deleted event ID: $id");
                echo json_encode(['success' => true, 'message' => 'Event and associated media deleted successfully.']);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Event not found.']);
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
