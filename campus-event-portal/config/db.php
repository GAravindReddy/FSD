<?php
// ── Database Configuration ──────────────────────────────────────
define('DB_HOST',    '127.0.0.1');
define('DB_PORT',    '3307');
define('DB_NAME',    'campus_event_portal');
define('DB_USER',    'root');
define('DB_PASS',    '');          // empty — confirmed from config.inc.php
define('DB_CHARSET', 'utf8mb4');

$dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    die("<pre style='background:#1a1a2e;color:#f87171;padding:2rem;font-size:1rem;font-family:monospace;'>
<b>Database Connection Failed</b>

Error : " . $e->getMessage() . "

Common Fixes:
 1. Make sure XAMPP MySQL is STARTED (green in XAMPP Control Panel)
 2. Open phpMyAdmin and run the SQL file to create the database
 3. If your MySQL has a password, set DB_PASS above to your password
 4. Confirm the database 'campus_event_portal' exists in phpMyAdmin
</pre>");
}