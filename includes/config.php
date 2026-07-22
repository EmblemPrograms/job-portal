<?php
/**
 * Central database configuration.
 *
 * Auto-selects credentials based on where the app is running, so you NEVER have to
 * edit code when moving between your local WAMP and the live host:
 *   - on localhost (WAMP)  -> local defaults below
 *   - on your live domain  -> the "LIVE HOST" block (or config.local.php override)
 */

// ---- Local development (WAMP / XAMPP) ----
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'job_portal';

// Detect whether we're running locally.
$httpHost = strtolower($_SERVER['HTTP_HOST'] ?? 'localhost');
$isLocal  = $httpHost === ''
         || strpos($httpHost, 'localhost') !== false
         || strpos($httpHost, '127.0.0.1') !== false
         || strpos($httpHost, '::1') !== false;

if (!$isLocal) {
    // ---- LIVE HOST (InfinityFree) ----
    // Get these from InfinityFree control panel -> "MySQL Databases".
    // (Or leave them and instead create includes/config.local.php — see below.)
    $DB_HOST = 'sqlXXX.infinityfree.com';   // e.g. sql200.infinityfree.com
    $DB_USER = 'if0_00000000';              // your MySQL username
    $DB_PASS = 'YOUR_DB_PASSWORD';          // your MySQL password
    $DB_NAME = 'if0_00000000_job_portal';   // your database name (prefixed)
}

// Optional: keep real credentials OUT of Git. If includes/config.local.php exists,
// the values it defines override everything above. This file is gitignored, so you
// create it directly on the server and your secrets never get committed.
if (file_exists(__DIR__ . '/config.local.php')) {
    require __DIR__ . '/config.local.php';
}
