<?php
// ============================================================
// config.php — Database connection settings
// ============================================================
// HOW TO USE:
//   Include this file at the top of every PHP page:
//   require_once 'config.php';
//   Then use $conn for all database queries.
// ============================================================

define('DB_HOST',     'localhost');
define('DB_USER',     'root');        // Default XAMPP username
define('DB_PASS',     '');            // Default XAMPP password (empty)
define('DB_NAME',     'event_management');
define('DB_CHARSET',  'utf8mb4');

// Create MySQLi connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$conn->set_charset(DB_CHARSET);

// Check connection
if ($conn->connect_error) {
    die(json_encode([
        'success' => false,
        'message' => 'Database connection failed: ' . $conn->connect_error
    ]));
}

// Session start (called once here so other pages don't need to)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Helper: sanitize input
function sanitize($conn, $data) {
    return $conn->real_escape_string(trim(htmlspecialchars($data)));
}

// Helper: redirect with message
function redirect($url, $msg = '', $type = 'success') {
    if ($msg) {
        $_SESSION['flash_msg']  = $msg;
        $_SESSION['flash_type'] = $type;
    }
    header("Location: $url");
    exit();
}

// Helper: check if logged in
function requireLogin() {
    if (empty($_SESSION['user_id'])) {
        redirect('index.php', 'Please log in to continue.', 'error');
    }
}

// Helper: check organizer role
function requireOrganizer() {
    requireLogin();
    if ($_SESSION['user_role'] !== 'organizer') {
        redirect('events.php', 'Access denied. Organizers only.', 'error');
    }
}
?>
