<?php
// ============================================================
// register_event.php — Register / Unregister for an event
// ============================================================
require_once 'config.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('events.php');
}

$event_id = intval($_POST['event_id'] ?? 0);
$action   = $_POST['action'] ?? '';
$user_id  = $_SESSION['user_id'];

if ($event_id <= 0) {
    redirect('events.php', 'Invalid event.', 'error');
}

if ($action === 'register') {
    // Check capacity
    $cap = $conn->query("SELECT capacity, (SELECT COUNT(*) FROM registrations WHERE event_id=$event_id) AS reg_count FROM events WHERE id=$event_id")->fetch_assoc();

    if (!$cap) {
        redirect('events.php', 'Event not found.', 'error');
    }
    if ($cap['reg_count'] >= $cap['capacity']) {
        redirect('events.php', 'Sorry, this event is full.', 'error');
    }

    $stmt = $conn->prepare("INSERT IGNORE INTO registrations (event_id, user_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $event_id, $user_id);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        redirect('events.php', 'You have successfully registered for the event!', 'success');
    } else {
        redirect('events.php', 'You are already registered for this event.', 'error');
    }
}

if ($action === 'unregister') {
    $stmt = $conn->prepare("DELETE FROM registrations WHERE event_id=? AND user_id=?");
    $stmt->bind_param("ii", $event_id, $user_id);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        redirect('events.php', 'You have unregistered from the event.', 'success');
    } else {
        redirect('events.php', 'Could not unregister. You may not have been registered.', 'error');
    }
}

redirect('events.php');
?>
