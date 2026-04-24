<?php
require_once 'config.php';
requireOrganizer();

$flash_msg  = $_SESSION['flash_msg']  ?? '';
$flash_type = $_SESSION['flash_type'] ?? '';
unset($_SESSION['flash_msg'], $_SESSION['flash_type']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventHub — Add Event</title>
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
        <h2>➕ Create New Event</h2>
        <p class="form-subtitle">Fill in the details below to publish a new event.</p>

        <form action="insert.php" method="POST" id="eventForm" novalidate>

            <div class="form-row">
                <div class="form-group">
                    <label for="title">Event Title <span class="required">*</span></label>
                    <input type="text" id="title" name="title" placeholder="e.g. Web Dev Seminar 2026" required>
                    <span class="error-msg" id="err_title"></span>
                </div>
                <div class="form-group">
                    <label for="event_type">Event Type <span class="required">*</span></label>
                    <select id="event_type" name="event_type" required>
                        <option value="">— Select Type —</option>
                        <option value="seminar">Seminar</option>
                        <option value="workshop">Workshop</option>
                        <option value="meeting">Meeting</option>
                        <option value="conference">Conference</option>
                        <option value="other">Other</option>
                    </select>
                    <span class="error-msg" id="err_event_type"></span>
                </div>
            </div>

            <div class="form-group">
                <label for="description">Description <span class="required">*</span></label>
                <textarea id="description" name="description" rows="4"
                          placeholder="Describe the event…" required></textarea>
                <span class="error-msg" id="err_description"></span>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="event_date">Date <span class="required">*</span></label>
                    <input type="date" id="event_date" name="event_date" required>
                    <span class="error-msg" id="err_event_date"></span>
                </div>
                <div class="form-group">
                    <label for="event_time">Time <span class="required">*</span></label>
                    <input type="time" id="event_time" name="event_time" required>
                    <span class="error-msg" id="err_event_time"></span>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="venue">Venue <span class="required">*</span></label>
                    <input type="text" id="venue" name="venue" placeholder="e.g. Room 301, CSE Building" required>
                    <span class="error-msg" id="err_venue"></span>
                </div>
                <div class="form-group">
                    <label for="capacity">Capacity <span class="required">*</span></label>
                    <input type="number" id="capacity" name="capacity" placeholder="50" min="1" max="10000" required>
                    <span class="error-msg" id="err_capacity"></span>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Publish Event</button>
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
