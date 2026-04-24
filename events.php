<?php
require_once 'config.php';
requireLogin();

$flash_msg  = $_SESSION['flash_msg']  ?? '';
$flash_type = $_SESSION['flash_type'] ?? '';
unset($_SESSION['flash_msg'], $_SESSION['flash_type']);

$is_organizer = ($_SESSION['user_role'] === 'organizer');
$user_id      = $_SESSION['user_id'];

// Search & filter
$search     = sanitize($conn, $_GET['search'] ?? '');
$type_filter = sanitize($conn, $_GET['type']  ?? '');

// Build query
$where = "WHERE 1=1";
if ($search)      $where .= " AND (e.title LIKE '%$search%' OR e.venue LIKE '%$search%')";
if ($type_filter) $where .= " AND e.event_type = '$type_filter'";

$sql = "
    SELECT e.*,
           u.full_name AS organizer_name,
           (SELECT COUNT(*) FROM registrations r WHERE r.event_id = e.id) AS reg_count,
           (SELECT COUNT(*) FROM registrations r WHERE r.event_id = e.id AND r.user_id = $user_id) AS user_registered
    FROM events e
    JOIN users u ON u.id = e.organizer_id
    $where
    ORDER BY e.event_date ASC
";
$events = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventHub — All Events</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
    <div class="nav-brand">📅 EventHub</div>
    <div class="nav-links">
        <span class="nav-user">👤 <?= htmlspecialchars($_SESSION['user_name']) ?>
            <span class="badge badge-<?= $is_organizer ? 'organizer' : 'participant' ?>">
                <?= ucfirst($_SESSION['user_role']) ?>
            </span>
        </span>
        <?php if ($is_organizer): ?>
            <a href="add_event.php" class="btn btn-sm btn-primary">+ Add Event</a>
        <?php endif; ?>
        <form action="auth.php" method="POST" style="display:inline">
            <input type="hidden" name="action" value="logout">
            <button type="submit" class="btn btn-sm btn-outline">Logout</button>
        </form>
    </div>
</nav>

<div class="container">

    <?php if ($flash_msg): ?>
        <div class="alert alert-<?= $flash_type ?>"><?= htmlspecialchars($flash_msg) ?></div>
    <?php endif; ?>

    <!-- PAGE HEADER -->
    <div class="page-header">
        <h1>Upcoming Events</h1>
        <p>Browse and register for events below.</p>
    </div>

    <!-- SEARCH & FILTER -->
    <form method="GET" class="filter-bar">
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search events by title or venue…">
        <select name="type">
            <option value="">All Types</option>
            <?php foreach (['seminar','workshop','meeting','conference','other'] as $t): ?>
                <option value="<?= $t ?>" <?= $type_filter === $t ? 'selected' : '' ?>><?= ucfirst($t) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-primary">Search</button>
        <a href="events.php" class="btn btn-outline">Reset</a>
    </form>

    <!-- EVENTS GRID -->
    <div class="events-grid">
        <?php if ($events->num_rows === 0): ?>
            <div class="empty-state">
                <div style="font-size:3rem">📭</div>
                <p>No events found.</p>
                <?php if ($is_organizer): ?>
                    <a href="add_event.php" class="btn btn-primary">Create First Event</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <?php while ($event = $events->fetch_assoc()): ?>
            <div class="event-card">
                <div class="event-type-badge type-<?= $event['event_type'] ?>"><?= ucfirst($event['event_type']) ?></div>
                <h3><?= htmlspecialchars($event['title']) ?></h3>
                <p class="event-desc"><?= htmlspecialchars(substr($event['description'], 0, 100)) ?>…</p>
                <div class="event-meta">
                    <span>📆 <?= date('d M Y', strtotime($event['event_date'])) ?></span>
                    <span>⏰ <?= date('h:i A', strtotime($event['event_time'])) ?></span>
                    <span>📍 <?= htmlspecialchars($event['venue']) ?></span>
                    <span>👥 <?= $event['reg_count'] ?>/<?= $event['capacity'] ?> registered</span>
                    <span>🧑‍💼 <?= htmlspecialchars($event['organizer_name']) ?></span>
                </div>
                <div class="event-actions">
                    <?php if (!$is_organizer): ?>
                        <?php if ($event['user_registered']): ?>
                            <span class="badge badge-registered">✔ Registered</span>
                            <form action="register_event.php" method="POST" style="display:inline">
                                <input type="hidden" name="action"   value="unregister">
                                <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline">Unregister</button>
                            </form>
                        <?php elseif ($event['reg_count'] >= $event['capacity']): ?>
                            <span class="badge badge-full">Full</span>
                        <?php else: ?>
                            <form action="register_event.php" method="POST" style="display:inline">
                                <input type="hidden" name="action"   value="register">
                                <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-primary">Register</button>
                            </form>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if ($is_organizer): ?>
                        <a href="update.php?id=<?= $event['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                        <form action="delete.php" method="POST" style="display:inline"
                              onsubmit="return confirm('Delete this event?')">
                            <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
</div>

<footer class="footer">
    <p>&copy; <?= date('Y') ?> EventHub &mdash; CSE 3120 Web Programming Lab &mdash; ULAB</p>
</footer>

<script src="script.js"></script>
</body>
</html>
