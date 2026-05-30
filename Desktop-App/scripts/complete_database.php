<?php
/**
 * Creates any missing tables after a partial database_complete.sql import.
 * Run: php scripts/complete_database.php
 */
require_once __DIR__ . '/../config/config.php';

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error . PHP_EOL);
}
$mysqli->set_charset(DB_CHARSET);

$statements = [
    "CREATE TABLE IF NOT EXISTS priest_duties (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

    "CREATE TABLE IF NOT EXISTS donations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        donor_name VARCHAR(100) NOT NULL,
        amount DECIMAL(10,2) NOT NULL,
        purpose VARCHAR(200) NOT NULL DEFAULT 'General',
        payment_method ENUM('card', 'online_transfer') NOT NULL DEFAULT 'card',
        donation_reference VARCHAR(50) NOT NULL,
        payment_status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY uq_donation_reference (donation_reference),
        INDEX idx_created_at (created_at),
        INDEX idx_payment_status (payment_status)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

    "CREATE TABLE IF NOT EXISTS announcements (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(200) NOT NULL,
        message TEXT NOT NULL,
        date DATE NOT NULL,
        created_by INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
        INDEX idx_date (date)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

    "CREATE TABLE IF NOT EXISTS festivals (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        date DATE NOT NULL,
        description TEXT,
        image_url VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_date (date)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

    "CREATE TABLE IF NOT EXISTS special_days (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(120) NOT NULL,
        day_date DATE NOT NULL,
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_special_day_date (day_date)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

    "CREATE TABLE IF NOT EXISTS password_resets (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        token_hash VARCHAR(64) NOT NULL UNIQUE,
        expires_at DATETIME NOT NULL,
        used_at DATETIME NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_password_resets_user (user_id),
        INDEX idx_password_resets_expiry (expires_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

    "CREATE TABLE IF NOT EXISTS registration_logs (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

    "CREATE TABLE IF NOT EXISTS pooja_requests (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        pooja_name VARCHAR(200) NOT NULL,
        preferred_date DATE NOT NULL,
        preferred_time_slot TIME NULL,
        special_requests TEXT NULL,
        status ENUM('pending','approved','rejected','scheduled') DEFAULT 'pending',
        admin_remarks TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_user_id (user_id),
        INDEX idx_status (status),
        INDEX idx_created_at (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

    "CREATE TABLE IF NOT EXISTS notification_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        recipient_phone VARCHAR(20) NOT NULL,
        message_type ENUM('booking_confirmation', 'duty_assignment', 'registration_approval') NOT NULL,
        message_content TEXT NOT NULL,
        status ENUM('sent', 'failed', 'pending') DEFAULT 'pending',
        sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_recipient_phone (recipient_phone),
        INDEX idx_message_type (message_type)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
];

foreach ($statements as $sql) {
    if (!$mysqli->query($sql)) {
        echo '[FAIL] ' . $mysqli->error . PHP_EOL;
        echo substr($sql, 0, 80) . '...' . PHP_EOL;
        exit(1);
    }
    if (preg_match('/CREATE TABLE IF NOT EXISTS (\w+)/', $sql, $m)) {
        echo '[OK]   Table ready: ' . $m[1] . PHP_EOL;
    }
}

// Seed users if empty
$users = (int) $mysqli->query('SELECT COUNT(*) AS c FROM users')->fetch_assoc()['c'];
if ($users === 0) {
    $mysqli->query("INSERT INTO users (name, email, password, role, phone, approval_status) VALUES
        ('Admin User', 'admin@kovil.com', '\$2y\$12\$35m3IOeUDTc31bRmX8wede7p9so9UXYiMypxVrbx.sNdnxOn3JMCG', 'management', '9876543210', 'approved'),
        ('Priest Sharma', 'priest@kovil.com', '\$2y\$12\$35m3IOeUDTc31bRmX8wede7p9so9UXYiMypxVrbx.sNdnxOn3JMCG', 'priest', '9876543211', 'approved'),
        ('Devotee Kumar', 'devotee@kovil.com', '\$2y\$12\$35m3IOeUDTc31bRmX8wede7p9so9UXYiMypxVrbx.sNdnxOn3JMCG', 'devotee', '9876543212', 'approved')");
    echo '[OK]   Seeded default users' . PHP_EOL;
}

$schedules = (int) $mysqli->query('SELECT COUNT(*) AS c FROM pooja_schedule')->fetch_assoc()['c'];
if ($schedules === 0) {
    $mysqli->query("INSERT INTO pooja_schedule (pooja_name, pooja_date, time_slot, status, description) VALUES
        ('Morning Abhishekam', DATE_ADD(CURDATE(), INTERVAL 7 DAY), '06:00 AM - 07:30 AM', 'available', 'Sacred bath to the deity'),
        ('Kalasandhi Pooja', DATE_ADD(CURDATE(), INTERVAL 7 DAY), '08:00 AM - 09:00 AM', 'available', 'Morning worship ceremony'),
        ('Uchikala Pooja', DATE_ADD(CURDATE(), INTERVAL 10 DAY), '12:00 PM - 01:00 PM', 'available', 'Noon worship ceremony'),
        ('Special Ganesh Pooja', DATE_ADD(CURDATE(), INTERVAL 14 DAY), '09:00 AM - 10:30 AM', 'available', 'Special worship to Lord Ganesh'),
        ('Lakshmi Kubera Pooja', DATE_ADD(CURDATE(), INTERVAL 21 DAY), '07:00 PM - 08:30 PM', 'available', 'Worship for prosperity')");
    echo '[OK]   Seeded sample pooja schedules' . PHP_EOL;
}

$festivals = (int) $mysqli->query('SELECT COUNT(*) AS c FROM festivals')->fetch_assoc()['c'];
if ($festivals === 0) {
    $mysqli->query("INSERT INTO festivals (name, date, description) VALUES
        ('Maha Shivaratri', '2026-03-15', 'Great night of Lord Shiva'),
        ('Ganesh Chaturthi', '2026-09-07', 'Celebration of Lord Ganesh'),
        ('Navaratri', '2026-10-03', 'Nine nights of divine worship')");
    echo '[OK]   Seeded sample festivals' . PHP_EOL;
}

$donations = (int) $mysqli->query('SELECT COUNT(*) AS c FROM donations')->fetch_assoc()['c'];
if ($donations === 0) {
    $mysqli->query("INSERT INTO donations (donor_name, amount, purpose, payment_method, donation_reference, payment_status) VALUES
        ('Ramesh Kumar', 5000.00, 'Temple Renovation', 'card', 'DONRAMESH001', 'completed'),
        ('Sita Devi', 2500.00, 'Daily Pooja Sponsorship', 'online_transfer', 'DONSITA002', 'completed'),
        ('Anonymous', 10000.00, 'Festival Celebration', 'card', 'DONANON003', 'completed'),
        ('Lakshman Prasad', 1500.00, 'Annadanam', 'card', 'DONLAKSH004', 'completed')");
    echo '[OK]   Seeded sample donations' . PHP_EOL;
}

echo 'Done. Run: php scripts/verify_database.php' . PHP_EOL;
