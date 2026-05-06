<?php
echo "<h2>Setting Up Scammer Reporting Database</h2>";

$host = 'localhost';
$user = 'marttop_scammer';
$pass = 'saeed@saif1122';
$dbname = 'marttop_scammer';

// Try to connect
$conn = new mysqli($host, $user, $pass);

if ($conn->connect_error) {
    die("<div style='color:red; padding:20px; background:#ffe6e6; border:2px solid red;'>
        <h3>❌ Connection Failed!</h3>
        <p>Error: " . $conn->connect_error . "</p>
        <p>Please check:</p>
        <ul>
            <li>Username: $user</li>
            <li>Password: saeed@saif1122</li>
            <li>Make sure user exists in cPanel MySQL</li>
        </ul>
        </div>");
}

echo "<div style='color:green; padding:10px; background:#e6ffe6;'>✅ Connected to MySQL server</div>";

// Create database if not exists
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql)) {
    echo "<div style='color:green; padding:10px; background:#e6ffe6;'>✅ Database '$dbname' created/verified</div>";
} else {
    echo "<div style='color:red; padding:10px; background:#ffe6e6;'>❌ Failed to create database: " . $conn->error . "</div>";
}

// Select database
$conn->select_db($dbname);

// Create reports table
$sql = "CREATE TABLE IF NOT EXISTS reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_name VARCHAR(100) NOT NULL,
    user_mobile VARCHAR(20) NOT NULL,
    user_email VARCHAR(100) NOT NULL,
    scammer_name VARCHAR(100) NOT NULL,
    scammer_mobile VARCHAR(20) NOT NULL,
    scammer_whatsapp VARCHAR(20),
    short_description TEXT NOT NULL,
    long_description TEXT NOT NULL,
    image1 VARCHAR(255),
    image2 VARCHAR(255),
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    report_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    approved_date TIMESTAMP NULL,
    approved_by VARCHAR(100) NULL
)";

if ($conn->query($sql)) {
    echo "<div style='color:green; padding:10px; background:#e6ffe6;'>✅ Reports table created</div>";
} else {
    echo "<div style='color:red; padding:10px; background:#ffe6e6;'>❌ Failed to create table: " . $conn->error . "</div>";
}

// Create admins table
$sql = "CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql)) {
    echo "<div style='color:green; padding:10px; background:#e6ffe6;'>✅ Admins table created</div>";
    
    // Add default admin
    $default_pass = password_hash('admin123', PASSWORD_DEFAULT);
    $sql = "INSERT IGNORE INTO admins (username, password, email) 
            VALUES ('admin', '$default_pass', 'admin@mart72.top')";
    
    if ($conn->query($sql)) {
        echo "<div style='color:green; padding:10px; background:#e6ffe6;'>✅ Default admin created</div>";
        echo "<p><strong>Default Admin Login:</strong></p>";
        echo "<ul>";
        echo "<li>Username: <strong>admin</strong></li>";
        echo "<li>Password: <strong>admin123</strong></li>";
        echo "</ul>";
        echo "<p style='color:red;'><strong>⚠️ Change this password after first login!</strong></p>";
    }
} else {
    echo "<div style='color:red; padding:10px; background:#ffe6e6;'>❌ Failed to create admins table: " . $conn->error . "</div>";
}

$conn->close();

echo "<hr>";
echo "<h3>🎉 Setup Complete!</h3>";
echo "<p><strong>Database Details:</strong></p>";
echo "<ul>";
echo "<li>Host: localhost</li>";
echo "<li>Database: marttop_scammer</li>";
echo "<li>Username: marttop_scammer</li>";
echo "<li>Password: saeed@saif1122</li>";
echo "</ul>";
echo "<p><a href='index.php' style='background:green; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;'>Go to Main Form</a></p>";
echo "<p><a href='admin.php' style='background:blue; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;'>Go to Admin Panel</a></p>";
?>