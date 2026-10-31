<?php
/**
 * Campus Portal - Secure Logout
 * Item 10: Session destruction and Remember-Me token invalidation
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';

$userId = $_SESSION['user_id'] ?? null;

if ($userId) {
    try {
        $pdo = Database::getConnection();
        // Clear remember token in database
        $pdo->prepare("UPDATE users SET remember_token = NULL, remember_expiry = NULL WHERE id = ?")->execute([$userId]);
        log_activity($userId, 'USER_LOGOUT', 'User signed out manually');
    } catch (Exception $e) {
        // Silently continue logout
    }
}

// 1. Clear session array
$_SESSION = [];

// 2. Clear session cookie
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

// 3. Clear remember-me cookie
setcookie('remember_me', '', time() - 3600, '/', '', false, true);

// 4. Destroy server session
session_destroy();

header('Location: ' . base_url('login.php?logged_out=1'));
exit;
