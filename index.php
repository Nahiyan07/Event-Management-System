<?php
require_once 'config.php';

// Redirect already-logged-in users
if (!empty($_SESSION['user_id'])) {
    redirect('events.php');
}

$flash_msg  = $_SESSION['flash_msg']  ?? '';
$flash_type = $_SESSION['flash_type'] ?? '';
unset($_SESSION['flash_msg'], $_SESSION['flash_type']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventHub — Event Management System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-page">

<div class="auth-wrapper">
    <div class="auth-brand">
        <div class="brand-icon">📅</div>
        <h1>EventHub</h1>
        <p>Professional Event Management</p>
    </div>

    <?php if ($flash_msg): ?>
        <div class="alert alert-<?= $flash_type ?>"><?= htmlspecialchars($flash_msg) ?></div>
    <?php endif; ?>

    <!-- Tab Toggle -->
    <div class="auth-tabs">
        <button class="tab-btn active" onclick="showTab('login')">Login</button>
        <button class="tab-btn" onclick="showTab('register')">Register</button>
    </div>

    <!-- LOGIN FORM -->
    <div id="tab-login" class="auth-form active">
        <h2>Welcome Back</h2>
        <form action="auth.php" method="POST" id="loginForm" novalidate>
            <input type="hidden" name="action" value="login">
            <div class="form-group">
                <label for="login_email">Email Address</label>
                <input type="email" id="login_email" name="email" placeholder="you@example.com" required>
                <span class="error-msg" id="err_login_email"></span>
            </div>
            <div class="form-group">
                <label for="login_password">Password</label>
                <input type="password" id="login_password" name="password" placeholder="Enter your password" required>
                <span class="error-msg" id="err_login_password"></span>
            </div>
            <button type="submit" class="btn btn-primary btn-full">Login</button>
        </form>
        <p class="auth-hint">Demo: <strong>organizer@demo.com</strong> / <strong>password123</strong></p>
    </div>

    <!-- REGISTER FORM -->
    <div id="tab-register" class="auth-form">
        <h2>Create Account</h2>
        <form action="auth.php" method="POST" id="registerForm" novalidate>
            <input type="hidden" name="action" value="register">
            <div class="form-group">
                <label for="reg_name">Full Name</label>
                <input type="text" id="reg_name" name="full_name" placeholder="Your full name" required>
                <span class="error-msg" id="err_reg_name"></span>
            </div>
            <div class="form-group">
                <label for="reg_email">Email Address</label>
                <input type="email" id="reg_email" name="email" placeholder="you@example.com" required>
                <span class="error-msg" id="err_reg_email"></span>
            </div>
            <div class="form-group">
                <label for="reg_password">Password</label>
                <input type="password" id="reg_password" name="password" placeholder="At least 6 characters" required>
                <span class="error-msg" id="err_reg_password"></span>
            </div>
            <div class="form-group">
                <label for="reg_confirm">Confirm Password</label>
                <input type="password" id="reg_confirm" name="confirm_password" placeholder="Re-enter password" required>
                <span class="error-msg" id="err_reg_confirm"></span>
            </div>
            <div class="form-group">
                <label for="reg_role">Register As</label>
                <select id="reg_role" name="role" required>
                    <option value="participant">Participant</option>
                    <option value="organizer">Organizer</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-full">Create Account</button>
        </form>
    </div>
</div>

<script src="script.js"></script>
</body>
</html>
