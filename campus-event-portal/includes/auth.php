<?php
/**
 * CampusVerse — Auth Guard
 * Include at the top of any protected page
 */

if (session_status() === PHP_SESSION_NONE) {
  session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'secure'   => isset($_SERVER['HTTPS']),
    'httponly' => true,
    'samesite' => 'Strict',
  ]);
  session_start();
}

/**
 * Require user login — redirects to login page if not authenticated
 */
function requireUser() {
  if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
  }
}

/**
 * Require admin login
 */
function requireAdmin() {
  if (!isset($_SESSION['admin_id'])) {
    header('Location: /login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
  }
}

/**
 * Generate a CSRF token
 */
function csrfToken(): string {
  if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
  }
  return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token from POST
 */
function verifyCsrf(): bool {
  return hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'] ?? '');
}

/**
 * Rate limiting (simple session-based)
 */
function rateLimit(string $action, int $maxAttempts = 5, int $windowSeconds = 300): bool {
  $key = 'rl_' . $action;
  $now = time();

  if (!isset($_SESSION[$key])) {
    $_SESSION[$key] = ['count' => 0, 'start' => $now];
  }

  if ($now - $_SESSION[$key]['start'] > $windowSeconds) {
    $_SESSION[$key] = ['count' => 0, 'start' => $now];
  }

  $_SESSION[$key]['count']++;
  return $_SESSION[$key]['count'] <= $maxAttempts;
}

/**
 * Sanitize output
 */
function e(string $str): string {
  return htmlspecialchars($str, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}