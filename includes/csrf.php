<?php
/**
 * CSRF Protection Token Helper
 *
 * Generates and validates cryptographic Anti-CSRF tokens for all state-changing POST requests.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Generate or retrieve current CSRF token
 */
function get_csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Render a hidden CSRF token input field for forms
 */
function csrf_field(): string
{
    $token = htmlspecialchars(get_csrf_token(), ENT_QUOTES, 'UTF-8');
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

/**
 * Validate submitted CSRF token
 *
 * @param string|null $submittedToken
 * @return bool
 */
function validate_csrf_token(?string $submittedToken): bool
{
    if (empty($submittedToken) || empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $submittedToken);
}

/**
 * Guard: Enforce valid CSRF token on POST requests
 */
function require_csrf(): void
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        if (!validate_csrf_token($token)) {
            http_response_code(403);
            die('Access Denied: Invalid or missing Anti-CSRF security token.');
        }
    }
}
