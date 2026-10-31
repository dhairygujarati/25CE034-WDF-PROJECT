<?php
/**
 * Campus Portal - Secure Authentication
 * Item 10: Secure Login/Logout with Sessions, Role-Based Access, Timeout, & Remember-Me Token
 * Spec: Includes a background image for the login page and glassmorphism styling
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/csrf.php';

// Redirect if already authenticated
if (is_logged_in()) {
    header('Location: ' . base_url('dashboard.php'));
    exit;
}

$error = '';
$timeoutAlert = isset($_GET['timeout']);
$loggedOutAlert = isset($_GET['logged_out']);
$registeredAlert = isset($_GET['registered']);

// Brute-force rate limiting check (5 attempts per 10 minutes)
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['last_attempt_time'] = time();
}

if ($_SESSION['login_attempts'] >= 5 && (time() - $_SESSION['last_attempt_time'] < 600)) {
    $remainingLock = 600 - (time() - $_SESSION['last_attempt_time']);
    $error = "Too many failed login attempts. Security lockout active for " . ceil($remainingLock / 60) . " more minute(s).";
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();

    $email      = trim($_POST['email'] ?? '');
    $password   = $_POST['password'] ?? '';
    $rememberMe = isset($_POST['remember_me']);

    if (empty($email) || empty($password)) {
        $error = 'Please provide both your registered email and password.';
    } else {
        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("
                SELECT id, full_name, email, password_hash, role 
                FROM users 
                WHERE email = :email 
                LIMIT 1
            ");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch();

            // Verify password using standard secure password_verify
            if ($user && password_verify($password, $user['password_hash'])) {
                // Success: Reset rate limiter
                $_SESSION['login_attempts'] = 0;

                // Session fixation prevention: Regenerate session ID
                session_regenerate_id(true);

                // Initialize authenticated session state
                $_SESSION['user_id']       = $user['id'];
                $_SESSION['user_name']     = $user['full_name'];
                $_SESSION['user_email']    = $user['email'];
                $_SESSION['user_role']     = $user['role'];
                $_SESSION['last_activity'] = time();

                // Item 10: Remember-Me Token Implementation
                if ($rememberMe) {
                    $rawToken = bin2hex(random_bytes(32));
                    $tokenHash = hash('sha256', $rawToken);
                    $expiry = date('Y-m-d H:i:s', time() + (86400 * 30)); // 30 Days

                    $updateToken = $pdo->prepare("
                        UPDATE users 
                        SET remember_token = :token, remember_expiry = :expiry, last_login = NOW() 
                        WHERE id = :id
                    ");
                    $updateToken->execute([
                        ':token'  => $tokenHash,
                        ':expiry' => $expiry,
                        ':id'     => $user['id']
                    ]);

                    // Set HttpOnly, SameSite=Lax cookie
                    setcookie('remember_me', $rawToken, [
                        'expires'  => time() + (86400 * 30),
                        'path'     => '/',
                        'secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
                        'httponly' => true,
                        'samesite' => 'Lax'
                    ]);
                } else {
                    // Update last login timestamp only
                    $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?")->execute([$user['id']]);
                }

                // Log audit trail
                log_activity($user['id'], 'USER_LOGIN', "Successful login as {$user['role']}");

                set_flash('success', "Welcome back, {$user['full_name']}!");
                header('Location: ' . base_url('dashboard.php'));
                exit;
            } else {
                // Failed attempt
                $_SESSION['login_attempts']++;
                $_SESSION['last_attempt_time'] = time();
                $error = 'Invalid credentials. Please verify your email and password.';
                log_activity(null, 'LOGIN_FAILED', "Failed login attempt for: $email");
            }
        } catch (Exception $e) {
            $error = 'Authentication service error: ' . $e->getMessage();
        }
    }
}

$pageTitle = 'Secure Login';
require_once __DIR__ . '/includes/header.php';
?>

<div class="auth-page-wrapper">
  <div class="auth-card">
    <div class="auth-header">
      <span class="badge badge-primary">Portal Access</span>
      <h1 style="margin-top: 0.5rem;">Campus Sign In</h1>
      <p style="color: var(--text-muted); font-size: 0.92rem;">
        Sign in to your CHARUSAT Student Portal account.
      </p>
    </div>

    <?php if ($timeoutAlert): ?>
      <div class="alert alert-warning" role="alert">
        &#x23F0; Your session expired after 15 minutes of inactivity. Please sign in again.
      </div>
    <?php endif; ?>

    <?php if ($loggedOutAlert): ?>
      <div class="alert alert-info" role="alert">
        &#x2713; You have successfully signed out.
      </div>
    <?php endif; ?>

    <?php if ($registeredAlert): ?>
      <div class="alert alert-success" role="alert">
        &#x2713; Registration successful! Please log in with your credentials.
      </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
      <div class="alert alert-danger" role="alert">
        <?= e($error) ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="<?= base_url('login.php') ?>" novalidate>
      <?= csrf_field() ?>

      <div class="form-group">
        <label for="email" class="form-label">Email Address <span class="required">*</span></label>
        <input 
          type="email" 
          id="email" 
          name="email" 
          class="form-control" 
          value="<?= e($_POST['email'] ?? '') ?>" 
          placeholder="admin@campus.edu or student@campus.edu" 
          required 
          autocomplete="email"
        >
      </div>

      <div class="form-group">
        <label for="password" class="form-label">Password <span class="required">*</span></label>
        <input 
          type="password" 
          id="password" 
          name="password" 
          class="form-control" 
          placeholder="••••••••••••" 
          required 
          autocomplete="current-password"
        >
      </div>

      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; font-size: 0.9rem;">
        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
          <input type="checkbox" name="remember_me" value="1" <?= isset($_POST['remember_me']) ? 'checked' : '' ?>>
          <span>Remember me (30 days)</span>
        </label>
        <a href="<?= base_url('contact.php') ?>" style="font-size: 0.85rem;">Need Help?</a>
      </div>

      <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.75rem;">
        &#x1F511; Sign In to Portal
      </button>

      <!-- Default Testing Accounts -->
      <div style="margin-top: 1.5rem; padding: 0.85rem 1rem; background: var(--bg-alt); border-radius: var(--radius-md); border: 1px solid var(--border-color); font-size: 0.85rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
          <strong style="color: var(--text-main);">Demo Accounts:</strong>
          <span style="font-size: 0.75rem; color: var(--text-muted);">Click to auto-fill</span>
        </div>
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
          <button type="button" class="btn btn-outline btn-sm" onclick="fillCreds('admin@campus.edu', 'Password@123')">
            Admin
          </button>
          <button type="button" class="btn btn-outline btn-sm" onclick="fillCreds('student@campus.edu', 'Password@123')">
            Student
          </button>
        </div>
      </div>

      <div style="text-align: center; margin-top: 1.5rem; font-size: 0.9rem; color: var(--text-muted);">
        Don't have an account yet? 
        <a href="<?= base_url('register.php') ?>" style="font-weight: 700;">Register as Student</a>
      </div>
    </form>
  </div>
</div>

<script>
function fillCreds(email, pass) {
  document.getElementById('email').value = email;
  document.getElementById('password').value = pass;
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
