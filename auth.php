<?php
// ============================================================
// auth.php — Handles Login & Registration
// ============================================================
require_once 'config.php';

$action = $_POST['action'] ?? '';

// ---- LOGIN ----
if ($action === 'login') {
    $email    = sanitize($conn, $_POST['email']    ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        redirect('index.php', 'Please fill in all fields.', 'error');
    }

    $stmt = $conn->prepare("SELECT id, full_name, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_role'] = $user['role'];
            redirect('events.php', 'Welcome back, ' . $user['full_name'] . '!', 'success');
        }
    }
    redirect('index.php', 'Invalid email or password.', 'error');
}

// ---- REGISTER ----
if ($action === 'register') {
    $full_name        = sanitize($conn, $_POST['full_name']        ?? '');
    $email            = sanitize($conn, $_POST['email']            ?? '');
    $password         = $_POST['password']         ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role             = in_array($_POST['role'] ?? '', ['organizer','participant']) ? $_POST['role'] : 'participant';

    if (empty($full_name) || empty($email) || empty($password)) {
        redirect('index.php?tab=register', 'Please fill in all fields.', 'error');
    }
    if ($password !== $confirm_password) {
        redirect('index.php?tab=register', 'Passwords do not match.', 'error');
    }
    if (strlen($password) < 6) {
        redirect('index.php?tab=register', 'Password must be at least 6 characters.', 'error');
    }

    // Check duplicate email
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        redirect('index.php?tab=register', 'Email already registered. Please login.', 'error');
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $full_name, $email, $hashed, $role);

    if ($stmt->execute()) {
        redirect('index.php', 'Registration successful! Please login.', 'success');
    } else {
        redirect('index.php?tab=register', 'Registration failed. Try again.', 'error');
    }
}

// ---- LOGOUT ----
if ($action === 'logout') {
    session_destroy();
    redirect('index.php', 'You have been logged out.', 'success');
}

redirect('index.php');
?>
