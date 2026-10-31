<?php
/**
 * Application Helpers & Utility Functions
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Determine dynamic base URL for links and assets
 * Auto-detects the project folder name — works regardless of what the
 * extracted folder is called (campus_portal, campus_portal_updated, etc.)
 */
function base_url(string $path = ''): string
{
    $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');

    // Dynamically detect the top-level project folder from the URL path.
    // e.g. /campus_portal/students/index.php  → base = /campus_portal/
    //      /campus_portal_updated/index.php   → base = /campus_portal_updated/
    //      /index.php (run from htdocs root)  → base = /
    $parts = explode('/', ltrim($scriptName, '/'));
    if (count($parts) > 1 && $parts[0] !== '') {
        $base = '/' . $parts[0] . '/';
    } else {
        $base = '/';
    }

    $trimmedPath = ltrim($path, '/');
    return $base . $trimmedPath;
}

/**
 * Sanitize output for safe HTML injection (XSS Prevention)
 */
function e(?string $string): string
{
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Set a session flash message
 */
function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = [
        'type'    => $type, // 'success', 'danger', 'warning', 'info'
        'message' => $message
    ];
}

/**
 * Retrieve and clear session flash message
 */
function get_flash(): ?array
{
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Log activity to database audit trail
 */
function log_activity(?int $userId, string $action, ?string $details = null): void
{
    try {
        require_once __DIR__ . '/../config/database.php';
        $pdo = Database::getConnection();
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';

        $stmt = $pdo->prepare("
            INSERT INTO activity_logs (user_id, action, details, ip_address)
            VALUES (:uid, :act, :det, :ip)
        ");
        $stmt->execute([
            ':uid' => $userId,
            ':act' => $action,
            ':det' => $details,
            ':ip'  => $ip
        ]);
    } catch (Exception $e) {
        // Suppress audit logging failures so main request continues safely
        error_log('[Activity Log Warning] ' . $e->getMessage());
    }
}
