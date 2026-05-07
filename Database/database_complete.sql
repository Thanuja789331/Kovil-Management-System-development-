-- ============================================
-- Kovil Management System - Complete Database
-- ============================================
-- This is the COMPLETE and FINAL database script
-- Includes all features: registration approval, booking enhancements, SMS logs
-- Run this ONCE to create a fresh database with all features
-- ============================================

-- Create and use the database
CREATE DATABASE IF NOT EXISTS kovil_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE kovil_db;

-- ============================================
-- CORE TABLES
-- ============================================

-- Users table with phone and approval status
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('devotee', 'priest', 'management') NOT NULL DEFAULT 'devotee',
    phone VARCHAR(20),
    approval_status ENUM('pending', 'approved', 'rejected') DEFAULT 'approved',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_approval_status (approval_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pooja Schedule table
CREATE TABLE IF NOT EXISTS pooja_schedule (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pooja_name VARCHAR(100) NOT NULL,
    pooja_date DATE NOT NULL,
    time_slot VARCHAR(50) NOT NULL,
    status ENUM('available', 'booked', 'completed') DEFAULT 'available',
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_pooja_date (pooja_date),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bookings table with enhanced features
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_reference VARCHAR(50) UNIQUE,
    schedule_id INT NOT NULL,
    user_id INT NOT NULL,
    devotee_phone VARCHAR(20),
    special_requests TEXT,
    notification_preference ENUM('sms', 'email', 'both') NOT NULL DEFAULT 'both',
    status ENUM('confirmed', 'cancelled') DEFAULT 'confirmed',
    confirmation_email_sent TINYINT(1) DEFAULT 0,
    reminder_10_day_email_sent TINYINT(1) DEFAULT 0,
    reminder_3_day_email_sent TINYINT(1) DEFAULT 0,
    sms_sent TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (schedule_id) REFERENCES pooja_schedule(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_booking_reference (booking_reference),
    INDEX idx_user_id (user_id),
    INDEX idx_schedule_id (schedule_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Priest Duties table
CREATE TABLE IF NOT EXISTS priest_duties (
    id INT AUTO_INCREMENT PRIMARY KEY,
    priest_id INT NOT NULL,
    schedule_id INT NOT NULL,
    assigned_date DATE NOT NULL,
    status ENUM('assigned', 'completed', 'cancelled') DEFAULT 'assigned',
    notification_sent TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (priest_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (schedule_id) REFERENCES pooja_schedule(id) ON DELETE CASCADE,
    INDEX idx_priest_id (priest_id),
    INDEX idx_assigned_date (assigned_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Donations table
CREATE TABLE IF NOT EXISTS donations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    donor_name VARCHAR(100) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    purpose VARCHAR(200),
    payment_status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_created_at (created_at),
    INDEX idx_payment_status (payment_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Announcements table
CREATE TABLE IF NOT EXISTS announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    date DATE NOT NULL,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_date (date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Festivals table
CREATE TABLE IF NOT EXISTS festivals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    date DATE NOT NULL,
    description TEXT,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_date (date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Special days table
CREATE TABLE IF NOT EXISTS special_days (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(120) NOT NULL,
    day_date DATE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_special_day_date (day_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Registration Logs table (audit trail)
CREATE TABLE IF NOT EXISTS registration_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    action ENUM('registered', 'approved', 'rejected') NOT NULL,
    performed_by INT,
    remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (performed_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_action (action)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Notification Logs table (SMS tracking)
CREATE TABLE IF NOT EXISTS notification_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    recipient_phone VARCHAR(20) NOT NULL,
    message_type ENUM('booking_confirmation', 'duty_assignment', 'registration_approval') NOT NULL,
    message_content TEXT NOT NULL,
    status ENUM('sent', 'failed', 'pending') DEFAULT 'pending',
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_recipient_phone (recipient_phone),
    INDEX idx_message_type (message_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- SAMPLE DATA
-- ============================================

-- Default Users (password for all: password123)
-- Password hash: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
-- Default Users (Password for all accounts: 'password')
-- Password hash generated using: password_hash('password', PASSWORD_DEFAULT)
INSERT INTO users (name, email, password, role, phone, approval_status) VALUES 
('Admin User', 'admin@kovil.com', '$2y$12$35m3IOeUDTc31bRmX8wede7p9so9UXYiMypxVrbx.sNdnxOn3JMCG', 'management', '9876543210', 'approved'),
('Priest Sharma', 'priest@kovil.com', '$2y$12$35m3IOeUDTc31bRmX8wede7p9so9UXYiMypxVrbx.sNdnxOn3JMCG', 'priest', '9876543211', 'approved'),
('Devotee Kumar', 'devotee@kovil.com', '$2y$12$35m3IOeUDTc31bRmX8wede7p9so9UXYiMypxVrbx.sNdnxOn3JMCG', 'devotee', '9876543212', 'approved');

-- Sample Pooja Schedules
INSERT INTO pooja_schedule (pooja_name, pooja_date, time_slot, status, description) VALUES
('Morning Abhishekam', '2026-03-23', '06:00 AM - 07:30 AM', 'available', 'Sacred bath to the deity'),
('Kalasandhi Pooja', '2026-03-23', '08:00 AM - 09:00 AM', 'available', 'Morning worship ceremony'),
('Uchikala Pooja', '2026-03-23', '12:00 PM - 01:00 PM', 'available', 'Noon worship ceremony'),
('Sayaraksha Pooja', '2026-03-23', '06:00 PM - 07:00 PM', 'available', 'Evening worship ceremony'),
('Arthajama Pooja', '2026-03-23', '08:30 PM - 09:00 PM', 'available', 'Night worship ceremony'),
('Special Ganesh Pooja', '2026-03-24', '09:00 AM - 10:30 AM', 'available', 'Special worship to Lord Ganesh'),
('Navaratri Homam', '2026-03-25', '10:00 AM - 12:00 PM', 'available', 'Sacred fire ceremony during Navaratri'),
('Rudra Abhishekam', '2026-03-26', '06:00 AM - 07:30 AM', 'available', 'Sacred bath with mantras'),
('Lakshmi Kubera Pooja', '2026-03-27', '07:00 PM - 08:30 PM', 'available', 'Worship for prosperity'),
('Vishnu Sahasranama', '2026-03-28', '08:00 AM - 09:30 AM', 'available', 'Chanting of 1000 names of Vishnu');

-- Sample Festivals
INSERT INTO festivals (name, date, description) VALUES
('Maha Shivaratri', '2026-03-15', 'Great night of Lord Shiva with special abhishekams and prayers'),
('Ganesh Chaturthi', '2026-09-07', 'Celebration of Lord Ganesh\'s birth with elaborate decorations'),
('Navaratri', '2026-10-03', 'Nine nights of divine feminine worship with Durga Saptashati recitation'),
('Diwali', '2026-11-18', 'Festival of lights with special lakshmi pooja and celebrations'),
('Tamil New Year', '2026-04-14', 'Traditional new year celebration with special poojas'),
('Thai Pongal', '2026-01-14', 'Sri Lankan and Tamil harvest thanksgiving festival'),
('Sinhala and Tamil New Year', '2026-04-14', 'Auspicious new year observance with temple blessings'),
('Vinayagar Chathurthi', '2026-08-27', 'Special Ganapathi pooja and homam'),
('Deepavali', '2026-10-20', 'Festival of lights with special Lakshmi pooja');

-- Sample Special Days
INSERT INTO special_days (title, day_date, description) VALUES
('Ekadashi', '2026-03-24', 'Fasting and special Vishnu prayers'),
('Pradosham', '2026-03-27', 'Special Shiva prayers during twilight'),
('Pournami', '2026-04-12', 'Full moon special deeparadhanai'),
('Aadi Amavasai', '2026-07-25', 'Ancestor remembrance and tharpanam'),
('Varalakshmi Vratham', '2026-08-08', 'Special Lakshmi pooja for prosperity'),
('Saraswathi Pooja', '2026-10-01', 'Pooja for knowledge, arts and learning'),
('Vijayadashami', '2026-10-02', 'Auspicious day for new beginnings'),
('Vesak Full Moon Day', '2026-05-12', 'Spiritual observance day with merit offerings'),
('Poson Full Moon Day', '2026-06-11', 'Auspicious full moon observance and prayers'),
('Esala Full Moon Day', '2026-07-10', 'Full moon observance with special temple programs'),
('Nikini Full Moon Day', '2026-08-16', 'Auspicious full moon day observance'),
('Binara Full Moon Day', '2026-09-14', 'Spiritual observance with special offerings'),
('Vap Full Moon Day', '2026-10-13', 'Auspicious day for meditation and worship'),
('Ill Full Moon Day', '2026-11-12', 'Full moon observance and devotional activities'),
('Unduvap Full Moon Day', '2026-12-11', 'Year-end full moon observance');

-- Sample Announcements
INSERT INTO announcements (title, message, date, created_by) VALUES
('Temple Timings Updated', 'The temple will now remain open from 6 AM to 9 PM daily for all devotees.', '2026-03-20', 1),
('Special Pooja This Weekend', 'Join us for a special Satyanarayana Pooja this Saturday at 10 AM.', '2026-03-21', 1),
('Annadanam Program', 'Free food distribution every Sunday at 12 PM. All are welcome.', '2026-03-22', 1);

-- Sample Donations
INSERT INTO donations (donor_name, amount, purpose, payment_status) VALUES
('Ramesh Kumar', 5000.00, 'Temple Renovation', 'completed'),
('Sita Devi', 2500.00, 'Daily Pooja Sponsorship', 'completed'),
('Anonymous', 10000.00, 'Festival Celebration', 'completed'),
('Lakshman Prasad', 1500.00, 'Annadanam', 'completed');

-- Sample Priest Duties
INSERT INTO priest_duties (priest_id, schedule_id, assigned_date, status) VALUES
(2, 1, '2026-03-23', 'assigned'),
(2, 3, '2026-03-23', 'assigned'),
(2, 5, '2026-03-23', 'assigned');

-- ============================================
-- VERIFICATION QUERIES
-- ============================================

-- Show all tables
SELECT 'Tables created successfully!' as status;
SHOW TABLES;

-- Count records in each table
SELECT 'users' as table_name, COUNT(*) as record_count FROM users
UNION ALL
SELECT 'pooja_schedule', COUNT(*) FROM pooja_schedule
UNION ALL
SELECT 'bookings', COUNT(*) FROM bookings
UNION ALL
SELECT 'priest_duties', COUNT(*) FROM priest_duties
UNION ALL
SELECT 'donations', COUNT(*) FROM donations
UNION ALL
SELECT 'announcements', COUNT(*) FROM announcements
UNION ALL
SELECT 'festivals', COUNT(*) FROM festivals;

-- Show test users
SELECT id, name, email, role, phone, approval_status FROM users ORDER BY id;

-- ============================================
-- LOGIN CREDENTIALS
-- ============================================
-- Admin: admin@kovil.com / password123
-- Priest: priest@kovil.com / password123
-- Devotee: devotee@kovil.com / password123
-- ============================================
