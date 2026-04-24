<?php
// ============================================================
// view.php — View all registrations (Organizer view)
// ============================================================
require_once 'config.php';
requireOrganizer();

$flash_msg  = $_SESSION['flash_msg']  ?? '';
$flash_type = $_SESSION['flash_type'] ?? '';
unset($_SESSION['flash_msg'], $_SESSION['flash_type']);

$sql = "
    SELECT e.title, e.event_date, e.venue,
           u.full_name, u.email,
           r.registered_at
    FROM registrations r
    JOIN events e ON e.id = r.event_id
    JOIN users  u ON u.id = r.user_id
    WHERE e.organizer_id = {$_SESSION['user_id']}
    ORDER BY e.event_date ASC, r.registered_at ASC
";
$registrations = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventHub — Registrations</title>
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

    <div class="page-header">
        <h1>📋 Event Registrations</h1>
        <p>All participants registered for your events.</p>
    </div>

    <div class="table-card">
        <?php if ($registrations->num_rows === 0): ?>
            <div class="empty-state">
                <div style="font-size:3rem">📭</div>
                <p>No registrations yet.</p>
            </div>
        <?php else: ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Event</th>
                        <th>Date</th>
                        <th>Venue</th>
                        <th>Participant</th>
                        <th>Email</th>
                        <th>Registered At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; while ($row = $registrations->fetch_assoc()): ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><?= htmlspecialchars($row['title']) ?></td>
                        <td><?= date('d M Y', strtotime($row['event_date'])) ?></td>
                        <td><?= htmlspecialchars($row['venue']) ?></td>
                        <td><?= htmlspecialchars($row['full_name']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= date('d M Y, h:i A', strtotime($row['registered_at'])) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<footer class="footer">
    <p>&copy; <?= date('Y') ?> EventHub &mdash; CSE 3120 Web Programming Lab</p>
</footer>
</body>
</html>
