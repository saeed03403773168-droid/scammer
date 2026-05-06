<?php
// This file creates necessary tables if they don't exist

// Admin users table
$conn->query("
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    full_name VARCHAR(100),
    role ENUM('admin', 'editor', 'viewer') DEFAULT 'admin',
    last_login TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");

// Check and insert default users
$check_users = $conn->query("SELECT COUNT(*) as count FROM admin_users");
if($check_users) {
    $row = $check_users->fetch_assoc();
    if($row['count'] == 0) {
        // Password for 'Mart727273'
        $hashed_password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';
        
        $conn->query("INSERT INTO admin_users (username, password, email, full_name, role) VALUES 
        ('saeed', '$hashed_password', 'saeed@marttop.com', 'Saeed Ahmed', 'admin'),
        ('admin', '$hashed_password', 'admin@marttop.com', 'Administrator', 'admin'),
        ('editor', '$hashed_password', 'editor@marttop.com', 'Content Editor', 'editor')");
    }
}

// Ads table
$conn->query("
CREATE TABLE IF NOT EXISTS ads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    video_url VARCHAR(500),
    video_file VARCHAR(500),
    thumbnail_url VARCHAR(500),
    duration INT DEFAULT 15,
    advertiser_name VARCHAR(100),
    category VARCHAR(50),
    contact_type ENUM('website', 'whatsapp', 'phone') DEFAULT 'website',
    contact_value VARCHAR(500),
    clicks INT DEFAULT 0,
    views INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    is_featured BOOLEAN DEFAULT FALSE,
    priority INT DEFAULT 0,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");

// Sponsors table
$conn->query("
CREATE TABLE IF NOT EXISTS sponsors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    image VARCHAR(500),
    website VARCHAR(500),
    whatsapp VARCHAR(20),
    phone VARCHAR(20),
    category VARCHAR(100),
    description TEXT,
    status TINYINT(1) DEFAULT 1,
    priority INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");

// Insert sample sponsors if empty
$check_sponsors = $conn->query("SELECT COUNT(*) as count FROM sponsors");
if($check_sponsors) {
    $row = $check_sponsors->fetch_assoc();
    if($row['count'] == 0) {
        $conn->query("INSERT INTO sponsors (name, website, category, status) VALUES
        ('Tech Solutions', 'https://techsolutions.com', 'Technology', 1),
        ('Mobile World', 'https://mobileworld.com', 'Mobile', 1),
        ('Digital Marketing Pro', 'https://digitalpro.com', 'Marketing', 1)");
    }
}

// Insert sample ads if empty
$check_ads = $conn->query("SELECT COUNT(*) as count FROM ads");
if($check_ads) {
    $row = $check_ads->fetch_assoc();
    if($row['count'] == 0) {
        $conn->query("INSERT INTO ads (title, description, video_url, thumbnail_url, duration, advertiser_name, category, contact_type, contact_value, is_featured) VALUES
        ('جدید موبائل ڈیلز', 'تازہ ترین موبائل آفرز اور ڈسکاؤنٹس دیکھیں', 'https://example.com/videos/mobile.mp4', 'https://via.placeholder.com/400x225/2c5364/ffffff?text=Mobile+Ad', 15, 'TechMart', 'موبائل', 'whatsapp', '923001234567', 1),
        ('کاروباری مواقع', 'آن لائن کاروبار کے بہترین مواقع', 'https://example.com/videos/business.mp4', 'https://via.placeholder.com/400x225/2c5364/ffffff?text=Business+Ad', 12, 'BusinessHub', 'کاروبار', 'website', 'https://businesshub.com', 1),
        ('تعلیمی پروگرام', 'آن لائن تعلیم کے نئے کورسز', 'https://example.com/videos/education.mp4', 'https://via.placeholder.com/400x225/2c5364/ffffff?text=Education+Ad', 15, 'EduLearn', 'تعلیم', 'phone', '02134567890', 0)");
    }
}
?>