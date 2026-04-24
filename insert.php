<?php
// ============================================================
// insert.php — Insert new event into database
// ============================================================
require_once 'config.php';
requireOrganizer();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('events.php');
}

$title       = sanitize($conn, $_POST['title']       ?? '');
$description = sanitize($conn, $_POST['description'] ?? '');
$event_type  = sanitize($conn, $_POST['event_type']  ?? '');
$event_date  = sanitize($conn, $_POST['event_date']  ?? '');
$event_time  = sanitize($conn, $_POST['event_time']  ?? '');
$venue       = sanitize($conn, $_POST['venue']       ?? '');
$capacity    = intval($_POST['capacity'] ?? 0);
$organizer_id = $_SESSION['user_id'];

// Server-side validation
$errors = [];
if (empty($title))       $errors[] = 'Title is required.';
if (empty($event_type))  $errors[] = 'Event type is required.';
if (empty($description)) $errors[] = 'Description is required.';
if (empty($event_date))  $errors[] = 'Event date is required.';
if (empty($event_time))  $errors[] = 'Event time is required.';
if (empty($venue))       $errors[] = 'Venue is required.';
if ($capacity < 1)       $errors[] = 'Capacity must be at least 1.';

// Date must be today or future
if (!empty($event_date) && strtotime($event_date) < strtotime(date('Y-m-d'))) {
    $errors[] = 'Event date cannot be in the past.';
}

if (!empty($errors)) {
    redirect('add_event.php', implode(' ', $errors), 'error');
}

$stmt = $conn->prepare("
    INSERT INTO events (title, description, event_type, event_date, event_time, venue, capacity, organizer_id)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");
$stmt->bind_param("ssssssii", $title, $description, $event_type, $event_date, $event_time, $venue, $capacity, $organizer_id);

if ($stmt->execute()) {
    redirect('events.php', 'Event "' . $title . '" has been published successfully!', 'success');
} else {
    redirect('add_event.php', 'Failed to save event. Please try again.', 'error');
}
?>
