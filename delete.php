<?php
// ============================================================
// delete.php — Delete an event (Organizer only)
// ============================================================
require_once 'config.php';
requireOrganizer();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('events.php');
}

$event_id = intval($_POST['event_id'] ?? 0);

if ($event_id <= 0) {
    redirect('events.php', 'Invalid event.', 'error');
}

// Only the organizer who created it can delete it
$stmt = $conn->prepare("DELETE FROM events WHERE id=? AND organizer_id=?");
$stmt->bind_param("ii", $event_id, $_SESSION['user_id']);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    redirect('events.php', 'Event deleted successfully.', 'success');
} else {
    redirect('events.php', 'Could not delete event. It may not exist or you do not have permission.', 'error');
}
?>
