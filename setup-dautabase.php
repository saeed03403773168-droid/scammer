<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🔧 Scammer Reporting System - Complete Setup</h2>";
echo "<p>This will create all necessary database tables.</p>";

$host = 'localhost';
$user = 'marttop_scammer';
$pass = 'saeed@saif1122';
$dbname = 'marttop_scammer';

// Connect to database
$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("<div style='color:red; padding:20px; background:#ffe6e6; border:2px solid red; border-radius:10px;'>
        <h3>❌ Database Connection Failed!</h3>
        <p>Error: " . $conn->connect_error . "</p>
        <p>Please check your database credentials in cPanel.</p>
    </div>");
}

echo "<div style='color:green; padding:15px; background:#e6ffe6; border-radius:10px;'>
        ✅ Connected to database: $dbname
      </div>";

// Array of tables to create
$tables = [
    'reports' => "CREATE TABLE IF NOT EXISTS reports (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
    
    'admins' => "CREATE TABLE IF NOT EXISTS admins (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
];

// Create tables
foreach ($tables as $table_name => $sql) {
    if ($conn->query($sql)) {
        echo "<div style='color:green; padding:10px; background:#e6ffe6; margin:5px; border-radius:5px;'>
                ✅ Table '$table_name' created successfully
              </div>";
    } else {
        echo "<div style='color:red; padding:10px; background:#ffe6e6; margin:5px; border-radius:5px;'>
                ❌ Failed to create table '$table_name': " . $conn->error . "
              </div>";
    }
}

// Check if admin exists, if not create one
$result = $conn->query("SELECT COUNT(*) as count FROM admins");
$row = $result->fetch_assoc();

if ($row['count'] == 0) {
    // Create default admin
    $default_password = password_hash('admin123', PASSWORD_DEFAULT);
    $sql = "INSERT INTO admins (username, password, email) VALUES ('admin', '$default_password', 'admin@mart72.top')";
    
    if ($conn->query($sql)) {
        echo "<div style='color:green; padding:15px; background:#e6ffe6; border-radius:10px; margin-top:20px;'>
                ✅ Default admin created successfully!
              </div>";
        echo "<div style='background:#fff3cd; padding:15px; border-radius:10px; margin-top:15px; border:2px solid #ffeaa7;'>
                <h3>📋 Default Admin Login:</h3>
                <ul style='font-size:16px;'>
                    <li><strong>Username:</strong> admin</li>
                    <li><strong>Password:</strong> admin123</li>
                </ul>
                <p style='color:red; font-weight:bold; margin-top:10px;'>⚠️ IMPORTANT: Change this password after first login!</p>
              </div>";
    } else {
        echo "<div style='color:red; padding:10px; background:#ffe6e6; border-radius:10px;'>
                ❌ Failed to create admin: " . $conn->error . "
              </div>";
    }
} else {
    echo "<div style='color:green; padding:15px; background:#e6ffe6; border-radius:10px; margin-top:20px;'>
            ✅ Admin table already has data
          </div>";
}

// Show all tables
echo "<h3 style='margin-top:30px;'>📊 Current Database Structure:</h3>";
$result = $conn->query("SHOW TABLES");
echo "<table style='width:100%; border-collapse:collapse; background:white; border-radius:10px; overflow:hidden; box-shadow:0 2px 10px rgba(0,0,0,0.1);'>
        <thead>
            <tr style='background:#667eea; color:white;'>
                <th style='padding:12px; text-align:left;'>Table Name</th>
                <th style='padding:12px; text-align:left;'>Rows</th>
            </tr>
        </thead>
        <tbody>";

while ($row = $result->fetch_array()) {
    $table_name = $row[0];
    $count_result = $conn->query("SELECT COUNT(*) as count FROM $table_name");
    $count_row = $count_result->fetch_assoc();
    $row_count = $count_row['count'];
    
    echo "<tr style='border-bottom:1px solid #eee;'>
            <td style='padding:12px;'>$table_name</td>
            <td style='padding:12px;'>$row_count</td>
          </tr>";
}

echo "</tbody></table>";

$conn->close();

echo "<hr style='margin:30px 0;'>";
echo "<h3>🎉 Setup Complete!</h3>";
echo "<p>Your scammer reporting system is now ready to use.</p>";
echo "<div style='margin-top:30px; display:flex; gap:15px; flex-wrap:wrap;'>";
echo "<a href='index.php' style='background:green; color:white; padding:12px 25px; text-decoration:none; border-radius:8px; font-weight:bold;'>
        🏠 Go to Main Form
      </a>";
echo "<a href='admin.php' style='background:blue; color:white; padding:12px 25px; text-decoration:none; border-radius:8px; font-weight:bold;'>
        🔐 Go to Admin Panel
      </a>";
echo "<a href='reports.php' style='background:#ff6b6b; color:white; padding:12px 25px; text-decoration:none; border-radius:8px; font-weight:bold;'>
        📋 View Reports
      </a>";
echo "</div>";

echo "<div style='margin-top:30px; padding:15px; background:#f8f9fa; border-radius:10px; border-left:4px solid #ff4757;'>
        <h4>📝 Next Steps:</h4>
        <ol>
            <li>Delete this setup.php file after successful setup</li>
            <li>Test the report form by submitting a test report</li>
            <li>Login to admin panel and approve/reject reports</li>
            <li>Check the public reports page</li>
        </ol>
      </div>";
?>