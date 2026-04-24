<?php
// ============================================================
// update.php — Show edit form & handle event update
// ============================================================
require_once 'config.php';
requireOrganizer();

$id = intval($_GET['id'] ?? 0);

// --- HANDLE POST (save update) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id          = intval($_POST['id'] ?? 0);
    $title       = sanitize($conn, $_POST['title']       ?? '');
    $description = sanitize($conn, $_POST['description'] ?? '');
    $event_type  = sanitize($conn, $_POST['event_type']  ?? '');
    $event_date  = sanitize($conn, $_POST['event_date']  ?? '');
    $event_time  = sanitize($conn, $_POST['event_time']  ?? '');
    $venue       = sanitize($conn, $_POST['venue']       ?? '');
    $capacity    = intval($_POST['capacity'] ?? 0);

    // Validation
    $errors = [];
    if (empty($title))       $errors[] = 'Title is required.';
    if (empty($event_type))  $errors[] = 'Event type is required.';
    if (empty($description)) $errors[] = 'Description is required.';
    if (empty($event_date))  $errors[] = 'Event date is required.';
    if (empty($event_time))  $errors[] = 'Event time is required.';
    if (empty($venue))       $errors[] = 'Venue is required.';
    if ($capacity < 1)       $errors[] = 'Capacity must be at least 1.';

    if (!empty($errors)) {
        redirect("update.php?id=$id", implode(' ', $errors), 'error');
    }

    $stmt = $conn->prepare("
        UPDATE events SET title=?, description=?, event_type=?, event_date=?, event_time=?, venue=?, capacity=?
        WHERE id=? AND organizer_id=?
    ");
    $stmt->bind_param("ssssssiis", $title, $description, $event_type, $event_date, $event_time, $venue, $capacity, $id, $_SESSION['user_id']);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        redirect('events.php', 'Event updated successfully!', 'success');
    } else {
        redirect("update.php?id=$id", 'Update failed or no changes made.', 'error');
    }
}

// --- SHOW FORM ---
if ($id <= 0) redirect('events.php');

$stmt = $conn->prepare("SELECT * FROM events WHERE id=? AND organizer_id=?");
$stmt->bind_param("ii", $id, $_SESSION['user_id']);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();

if (!$event) redirect('events.php', 'Event not found or access denied.', 'error');

$flash_msg  = $_SESSION['flash_msg']  ?? '';
$flash_type = $_SESSION['flash_type'] ?? '';
unset($_SESSION['flash_msg'], $_SESSION['flash_type']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventHub — Edit Event</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<nav class="navbar">
    <div class="nav-brand">📅 EventHub</div>
    <div class="nav-links">
        <a href="events.php" class="btn btn-sm btn-outline">← Back to Events</a>
    </div>
</nav>

<div class="container">
    <?php if ($flash_msg): ?>
        <div class="alert alert-<?= $flash_type ?>"><?= htmlspecialchars($flash_msg) ?></div>
    <?php endif; ?>

    <div class="form-card">
        <h2>✏️ Edit Event</h2>
        <p class="form-subtitle">Update the event details below.</p>

        <form action="update.php" method="POST" id="eventForm" novalidate>
            <input type="hidden" name="id" value="<?= $event['id'] ?>">

            <div class="form-row">
                <div class="form-group">
                    <label for="title">Event Title <span class="required">*</span></label>
                    <input type="text" id="title" name="title" value="<?= htmlspecialchars($event['title']) ?>" required>
                    <span class="error-msg" id="err_title"></span>
                </div>
                <div class="form-group">
                    <label for="event_type">Event Type <span class="required">*</span></label>
                    <select id="event_type" name="event_type" required>
                        <?php foreach (['seminar','workshop','meeting','conference','other'] as $t): ?>
                            <option value="<?= $t ?>" <?= $event['event_type'] === $t ? 'selected' : '' ?>>
                                <?= ucfirst($t) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="description">Description <span class="required">*</span></label>
                <textarea id="description" name="description" rows="4" required><?= htmlspecialchars($event['description']) ?></textarea>
                <span class="error-msg" id="err_description"></span>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="event_date">Date <span class="required">*</span></label>
                    <input type="date" id="event_date" name="event_date" value="<?= $event['event_date'] ?>" required>
                    <span class="error-msg" id="err_event_date"></span>
                </div>
                <div class="form-group">
                    <label for="event_time">Time <span class="required">*</span></label>
                    <input type="time" id="event_time" name="event_time" value="<?= $event['event_time'] ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="venue">Venue <span class="required">*</span></label>
                    <input type="text" id="venue" name="venue" value="<?= htmlspecialchars($event['venue']) ?>" required>
                    <span class="error-msg" id="err_venue"></span>
                </div>
                <div class="form-group">
                    <label for="capacity">Capacity <span class="required">*</span></label>
                    <input type="number" id="capacity" name="capacity" value="<?= $event['capacity'] ?>" min="1" required>
                    <span class="error-msg" id="err_capacity"></span>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="events.php" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>

<footer class="footer">
    <p>&copy; <?= date('Y') ?> EventHub &mdash; CSE 3120 Web Programming Lab</p>
</footer>

<script src="script.js"></script>
</body>
</html>
