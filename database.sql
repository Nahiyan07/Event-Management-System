-- ============================================================
-- Event Management System - Database Setup
-- Course: Web Programming (CSE 3120) | Spring 2026
-- University of Liberal Arts Bangladesh
-- ============================================================
-- INSTRUCTIONS:
--   1. Open phpMyAdmin at http://localhost/phpmyadmin
--   2. Click "New" to create a new database OR run the CREATE DATABASE line below
--   3. Select the database "event_management"
--   4. Click the "Import" tab
--   5. Choose this file and click "Go"
-- ============================================================

CREATE DATABASE IF NOT EXISTS event_management;
USE event_management;

-- -------------------------------------------------------
-- Table: users (Organizers & Participants)
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    full_name   VARCHAR(100)  NOT NULL,
    email       VARCHAR(150)  NOT NULL UNIQUE,
    password    VARCHAR(255)  NOT NULL,
    role        ENUM('organizer','participant') NOT NULL DEFAULT 'participant',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- -------------------------------------------------------
-- Table: events
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS events (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    title        VARCHAR(200)  NOT NULL,
    description  TEXT,
    event_type   ENUM('seminar','workshop','meeting','conference','other') NOT NULL DEFAULT 'seminar',
    event_date   DATE          NOT NULL,
    event_time   TIME          NOT NULL,
    venue        VARCHAR(200)  NOT NULL,
    capacity     INT           NOT NULL DEFAULT 50,
    organizer_id INT           NOT NULL,
    created_at   TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (organizer_id) REFERENCES users(id) ON DELETE CASCADE
);

-- -------------------------------------------------------
-- Table: registrations
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS registrations (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    event_id       INT NOT NULL,
    user_id        INT NOT NULL,
    registered_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_registration (event_id, user_id),
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id)  REFERENCES users(id)  ON DELETE CASCADE
);

-- -------------------------------------------------------
-- Sample Data
-- -------------------------------------------------------

-- Sample users (passwords are bcrypt of "password123")
INSERT INTO users (full_name, email, password, role) VALUES
('Admin Organizer',  'organizer@demo.com',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'organizer'),
('Alice Participant','alice@demo.com',         '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'participant'),
('Bob Participant',  'bob@demo.com',           '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'participant');

-- Sample events
INSERT INTO events (title, description, event_type, event_date, event_time, venue, capacity, organizer_id) VALUES
('Web Development Seminar',   'A seminar covering modern web development practices including HTML5, CSS3, and JavaScript ES6+.', 'seminar',    '2026-05-15', '10:00:00', 'Room 301, CSE Building', 60, 1),
('PHP & MySQL Workshop',      'Hands-on workshop on building dynamic websites with PHP and MySQL using XAMPP.', 'workshop',   '2026-05-22', '14:00:00', 'Computer Lab 2',         30, 1),
('Project Review Meeting',    'End-of-semester project review meeting for all CSE 3120 students.', 'meeting',    '2026-05-30', '09:00:00', 'Conference Hall A',      100, 1),
('AI in Education Conference','Exploring the impact of artificial intelligence on modern education systems.', 'conference', '2026-06-05', '11:00:00', 'Auditorium, Main Block',  200, 1);

-- Sample registrations
INSERT INTO registrations (event_id, user_id) VALUES
(1, 2),
(1, 3),
(2, 2);
