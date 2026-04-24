// ============================================================
// script.js — Client-side Validation & UI Interactions
// CSE 3120 Web Programming | EventHub
// ============================================================

// ---------- TAB SWITCHER (Login / Register) ----------
function showTab(tabName) {
    // Hide all auth forms
    document.querySelectorAll('.auth-form').forEach(f => f.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));

    // Show selected
    const form = document.getElementById('tab-' + tabName);
    if (form) form.classList.add('active');

    // Highlight button
    const buttons = document.querySelectorAll('.tab-btn');
    buttons.forEach(btn => {
        if (btn.textContent.toLowerCase().includes(tabName)) {
            btn.classList.add('active');
        }
    });
}

// Auto-open register tab if URL has ?tab=register
if (window.location.search.includes('tab=register')) {
    showTab('register');
}

// ---------- HELPER FUNCTIONS ----------

// Show an error message under a field
function showError(fieldId, message) {
    const errEl = document.getElementById('err_' + fieldId);
    const input = document.getElementById(fieldId);
    if (errEl) errEl.textContent = message;
    if (input) input.classList.add('input-error');
}

// Clear an error message
function clearError(fieldId) {
    const errEl = document.getElementById('err_' + fieldId);
    const input = document.getElementById(fieldId);
    if (errEl) errEl.textContent = '';
    if (input) input.classList.remove('input-error');
}

// Check if a string is a valid email
function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

// ---------- LOGIN FORM VALIDATION ----------
const loginForm = document.getElementById('loginForm');
if (loginForm) {
    loginForm.addEventListener('submit', function (e) {
        let valid = true;

        const email    = document.getElementById('login_email').value.trim();
        const password = document.getElementById('login_password').value;

        clearError('login_email');
        clearError('login_password');

        if (!email) {
            showError('login_email', 'Email address is required.');
            valid = false;
        } else if (!isValidEmail(email)) {
            showError('login_email', 'Please enter a valid email address.');
            valid = false;
        }

        if (!password) {
            showError('login_password', 'Password is required.');
            valid = false;
        }

        if (!valid) e.preventDefault();
    });
}

// ---------- REGISTER FORM VALIDATION ----------
const registerForm = document.getElementById('registerForm');
if (registerForm) {
    registerForm.addEventListener('submit', function (e) {
        let valid = true;

        const name     = document.getElementById('reg_name').value.trim();
        const email    = document.getElementById('reg_email').value.trim();
        const password = document.getElementById('reg_password').value;
        const confirm  = document.getElementById('reg_confirm').value;

        clearError('reg_name');
        clearError('reg_email');
        clearError('reg_password');
        clearError('reg_confirm');

        if (!name || name.length < 3) {
            showError('reg_name', 'Full name must be at least 3 characters.');
            valid = false;
        }

        if (!email) {
            showError('reg_email', 'Email address is required.');
            valid = false;
        } else if (!isValidEmail(email)) {
            showError('reg_email', 'Please enter a valid email address.');
            valid = false;
        }

        if (!password || password.length < 6) {
            showError('reg_password', 'Password must be at least 6 characters.');
            valid = false;
        }

        if (password !== confirm) {
            showError('reg_confirm', 'Passwords do not match.');
            valid = false;
        }

        if (!valid) e.preventDefault();
    });
}

// ---------- EVENT FORM VALIDATION (Add / Edit) ----------
const eventForm = document.getElementById('eventForm');
if (eventForm) {
    eventForm.addEventListener('submit', function (e) {
        let valid = true;

        // Fields to validate: [fieldId, friendlyName, minLength]
        const textFields = [
            ['title',       'Event title',   3],
            ['description', 'Description',   10],
            ['venue',       'Venue',         3],
        ];

        textFields.forEach(([id, label, min]) => {
            clearError(id);
            const val = document.getElementById(id);
            if (!val) return;
            if (!val.value.trim() || val.value.trim().length < min) {
                showError(id, label + ' must be at least ' + min + ' characters.');
                valid = false;
            }
        });

        // Event type
        clearError('event_type');
        const typeEl = document.getElementById('event_type');
        if (typeEl && !typeEl.value) {
            showError('event_type', 'Please select an event type.');
            valid = false;
        }

        // Date validation
        clearError('event_date');
        const dateEl = document.getElementById('event_date');
        if (dateEl) {
            if (!dateEl.value) {
                showError('event_date', 'Event date is required.');
                valid = false;
            } else {
                const selected = new Date(dateEl.value);
                const today    = new Date();
                today.setHours(0, 0, 0, 0);
                if (selected < today) {
                    showError('event_date', 'Event date cannot be in the past.');
                    valid = false;
                }
            }
        }

        // Capacity
        clearError('capacity');
        const capEl = document.getElementById('capacity');
        if (capEl) {
            const cap = parseInt(capEl.value);
            if (!capEl.value || isNaN(cap) || cap < 1) {
                showError('capacity', 'Capacity must be a positive number.');
                valid = false;
            } else if (cap > 10000) {
                showError('capacity', 'Capacity cannot exceed 10,000.');
                valid = false;
            }
        }

        if (!valid) e.preventDefault();
    });

    // Set minimum date to today for date input
    const dateInput = document.getElementById('event_date');
    if (dateInput && !dateInput.value) {
        const today = new Date().toISOString().split('T')[0];
        dateInput.setAttribute('min', today);
    }
}

// ---------- AUTO-DISMISS ALERTS ----------
document.querySelectorAll('.alert').forEach(function (alert) {
    setTimeout(function () {
        alert.style.transition = 'opacity 0.5s';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
    }, 4000); // dismiss after 4 seconds
});

// ---------- CHARACTER COUNTER FOR DESCRIPTION ----------
const descEl = document.getElementById('description');
if (descEl) {
    // Create counter element
    const counter = document.createElement('small');
    counter.style.cssText = 'color:#64748b; float:right; margin-top:4px;';
    descEl.parentNode.appendChild(counter);

    const update = () => {
        const len = descEl.value.length;
        counter.textContent = len + ' characters';
        counter.style.color = len < 10 ? '#dc2626' : '#64748b';
    };

    descEl.addEventListener('input', update);
    update(); // run once on load
}
