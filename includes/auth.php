<?php
/**
 * Authentication & Role-Based Access Control (RBAC) Engine
 *
 * Implements:
 * - Session initialization with secure cookie parameters
 * - Inactivity Session Timeout (15 minutes)
 * - Cryptographically Secure Remember-Me Token handling
 * - RBAC enforcement guards (Admin vs Student)
 */

if (session_status() === PHP_SESSION_NONE) {
    // Enforce modern secure session settings
    ini_set('session.cookie_httponly', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_samesite', 'Lax');
    session_start();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/helpers.php';

// Inactivity timeout constant: 15 minutes = 900 seconds
define('SESSION_TIMEOUT_SECONDS', 900);

/**
 * Check inactivity session timeout and remember-me auto login
 */
function check_session_security(): void
{
    $now = time();

    // 1. If user is logged in, check inactivity timeout
    if (isset($_SESSION['user_id'])) {
        if (isset($_SESSION['last_activity']) && ($now - $_SESSION['last_activity'] > SESSION_TIMEOUT_SECONDS)) {
            // Session expired due to inactivity
            $userId = $_SESSION['user_id'];
            log_activity($userId, 'SESSION_TIMEOUT', 'Automatic logout due to 15 min inactivity');
            
            // Clean session data
            $_SESSION = [];
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
            session_destroy();

            // Redirect with friendly timeout notification
            header('Location: ' . base_url('login.php?timeout=1'));
            exit;
        }

        // Update timestamp for active session
        $_SESSION['last_activity'] = $now;
        return;
    }

    // 2. If user is NOT logged in, attempt auto-login via remember-me cookie
    if (empty($_SESSION['user_id']) && !empty($_COOKIE['remember_me'])) {
        $rawToken = $_COOKIE['remember_me'];
        $tokenHash = hash('sha256', $rawToken);

        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("
                SELECT id, full_name, email, role, remember_expiry 
                FROM users 
                WHERE remember_token = :token 
                LIMIT 1
            ");
            $stmt->execute([':token' => $tokenHash]);
            $user = $stmt->fetch();

            if ($user && (!empty($user['remember_expiry']) && strtotime($user['remember_expiry']) > $now)) {
                // Token is valid and unexpired - regenerate session
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['last_activity'] = $now;

                log_activity($user['id'], 'AUTO_LOGIN', 'Authenticated via Remember-Me token');
            } else {
                // Stale or invalid cookie, clear it
                setcookie('remember_me', '', time() - 3600, '/', '', false, true);
            }
        } catch (Exception $e) {
            // Silently ignore DB errors during auto-login check
        }
    }
}

// Execute security check on script load
check_session_security();

/**
 * Check if current user is logged in
 */
function is_logged_in(): bool
{
    return !empty($_SESSION['user_id']);
}

/**
 * Get current authenticated user details
 */
function current_user(): ?array
{
    if (!is_logged_in()) {
        return null;
    }

    return [
        'id'    => $_SESSION['user_id'],
        'name'  => $_SESSION['user_name'] ?? 'User',
        'email' => $_SESSION['user_email'] ?? '',
        'role'  => $_SESSION['user_role'] ?? 'student',
    ];
}

/**
 * Check if logged-in user possesses a specific role
 */
function has_role(string $role): bool
{
    return is_logged_in() && (($_SESSION['user_role'] ?? '') === $role);
}

/**
 * Access Guard: Require authentication
 */
function require_login(): void
{
    if (!is_logged_in()) {
        set_flash('warning', 'Please sign in to access that page.');
        header('Location: ' . base_url('login.php'));
        exit;
    }
}

/**
 * Access Guard: Require specific role (RBAC)
 */
function require_role(string $role): void
{
    require_login();

    if (!has_role($role)) {
        set_flash('danger', 'Access Denied: You do not have administrative privileges to view that resource.');
        header('Location: ' . base_url('dashboard.php'));
        exit;
    }
}
