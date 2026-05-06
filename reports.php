<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'marttop_scammer');
define('DB_PASS', 'saeed@saif1122');
define('DB_NAME', 'marttop_scammer');

// Database connection function
function db_connect() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die("<div style='padding:30px; background:#ffebee; border:3px solid red; border-radius:15px; margin:20px; text-align:center;'>
            <h2 style='color:red;'>⚠️ Database Connection Error</h2>
            <p>Could not connect to database. Error: " . $conn->connect_error . "</p>
            <p>Please check your database credentials.</p>
        </div>");
    }
    
    return $conn;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = db_connect();
    
    // Create uploads directory if not exists
    if (!is_dir('uploads')) {
        mkdir('uploads', 0755, true);
    }
    
    // Process file uploads
    $image1 = [];
    $image2 = [];

    // Image1 uploads
    if (!empty($_FILES['image1']['name'][0])) {
        foreach ($_FILES['image1']['tmp_name'] as $key => $tmp) {
            if ($_FILES['image1']['error'][$key] === 0) {
                $name = time().'_'.basename($_FILES['image1']['name'][$key]);
                $path = 'uploads/'.$name;
                if (move_uploaded_file($tmp, $path)) {
                    $image1[] = $path;
                }
            }
        }
    }

    // Image2 uploads
    if (!empty($_FILES['image2']['name'][0])) {
        foreach ($_FILES['image2']['tmp_name'] as $key => $tmp) {
            if ($_FILES['image2']['error'][$key] === 0) {
                $name = time().'_'.basename($_FILES['image2']['name'][$key]);
                $path = 'uploads/'.$name;
                if (move_uploaded_file($tmp, $path)) {
                    $image2[] = $path;
                }
            }
        }
    }

    $image1 = implode(',', $image1);
    $image2 = implode(',', $image2);
    
    // Prepare SQL
    $stmt = $conn->prepare("INSERT INTO reports (
        user_name, user_mobile, user_email,
        scammer_name, scammer_mobile, scammer_whatsapp,
        short_description, long_description, image1, image2
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    if ($stmt) {
        $stmt->bind_param(
            "ssssssssss",
            $_POST['user_name'],
            $_POST['user_mobile'],
            $_POST['user_email'],
            $_POST['scammer_name'],
            $_POST['scammer_mobile'],
            $_POST['scammer_whatsapp'],
            $_POST['short_description'],
            $_POST['long_description'],
            $image1,
            $image2
        );
        
        if ($stmt->execute()) {
            $report_id = $stmt->insert_id;
            $success = "✅ Report submitted successfully! Report ID: #$report_id";
        } else {
            $error = "❌ Error: " . $stmt->error;
        }
        
        $stmt->close();
    } else {
        $error = "❌ Database error: " . $conn->error;
    }
    
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Report Scammer | mart72.top</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: #f0f4f8;
            color: #1e2a3a;
        }
        
        h1, h2, h3, .header h1, .sidebar-title, .submit-btn, .nav-links a {
            font-family: 'Poppins', sans-serif;
        }
        
        /* ================= HEADER (Blue theme) ================= */
        .header {
            background: linear-gradient(135deg, #0b3b6b, #1e4a7a);
            color: white;
            padding: 20px 15px;
            text-align: center;
            box-shadow: 0 6px 20px rgba(0,0,0,0.2);
        }
        
        .logo {
            display: flex;
            justify-content: center;
            margin-bottom: 12px;
        }
        
        .logo img {
            width: 90px;
            background: #fff;
            padding: 6px;
            border-radius: 16px;
            border: 2px solid #6ea8fe;
            box-shadow: 0 6px 20px rgba(0,0,0,0.25);
            transition: 0.3s;
        }
        
        .logo img:hover {
            transform: scale(1.05);
        }
        
        .header h1 {
            font-size: 1.8rem;
            margin-bottom: 8px;
        }
        
        .subtitle {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        .nav-links {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
        }
        
        .nav-links a {
            color: #fff;
            text-decoration: none;
            border: 1px solid rgba(255,255,255,0.5);
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 13px;
            font-weight: 500;
            transition: 0.2s;
        }
        
        .nav-links a:hover {
            background: #6ea8fe;
            color: #0b3b6b;
            border-color: #6ea8fe;
        }
        
        /* ================= CONTAINER ================= */
        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 30px;
        }
        
        /* Main form card */
        .main-content {
            background: #fff;
            border-radius: 32px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.08);
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }
        
        .form-container {
            padding: 30px;
        }
        
        .form-section {
            margin-bottom: 35px;
            padding-bottom: 25px;
            border-bottom: 1px solid #eef2f6;
        }
        
        .section-title {
            color: #1e4a7a;
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-left: 5px solid #1e4a7a;
            padding-left: 15px;
        }
        
        .section-title i {
            color: #1e4a7a;
        }
        
        .form-group {
            margin-bottom: 25px;
            position: relative;
        }
        
        .form-row {
            display: flex;
            gap: 25px;
            margin-bottom: 25px;
        }
        
        @media (max-width: 768px) {
            .form-row {
                flex-direction: column;
                gap: 15px;
            }
        }
        
        .form-row .form-group {
            flex: 1;
        }
        
        label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: #1e2a3a;
            font-size: 0.9rem;
        }
        
        .input-icon {
            position: absolute;
            left: 15px;
            top: 42px;
            color: #94a3b8;
            font-size: 18px;
        }
        
        input, textarea, select {
            width: 100%;
            padding: 12px 18px 12px 45px;
            border: 2px solid #e2e8f0;
            border-radius: 16px;
            font-size: 15px;
            transition: all 0.2s;
            background: #f8fafc;
            font-family: 'Inter', sans-serif;
        }
        
        input:focus, textarea:focus, select:focus {
            outline: none;
            border-color: #1e4a7a;
            background: white;
            box-shadow: 0 0 0 3px rgba(30,74,122,0.1);
        }
        
        textarea {
            min-height: 130px;
            resize: vertical;
            line-height: 1.5;
        }
        
        /* Upload area */
        .upload-area {
            border: 2px dashed #cbd5e1;
            border-radius: 20px;
            padding: 30px 20px;
            text-align: center;
            margin-top: 10px;
            background: #f8fafc;
            transition: all 0.2s;
            cursor: pointer;
        }
        
        .upload-area:hover {
            border-color: #1e4a7a;
            background: #f1f5f9;
        }
        
        .upload-area.dragover {
            border-color: #1e4a7a;
            background: #eef2ff;
        }
        
        .upload-icon {
            font-size: 48px;
            color: #94a3b8;
            margin-bottom: 12px;
        }
        
        .upload-text {
            color: #64748b;
            margin-bottom: 12px;
            font-size: 14px;
        }
        
        .upload-btn {
            background: linear-gradient(135deg, #1e4a7a, #0b3b6b);
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 40px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
        }
        
        .upload-btn:hover {
            background: linear-gradient(135deg, #0b3b6b, #072c4a);
            transform: scale(1.02);
        }
        
        .image-preview {
            display: flex;
            gap: 15px;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        
        .preview-item {
            width: 140px;
            height: 140px;
            border-radius: 16px;
            overflow: hidden;
            border: 2px solid #e2e8f0;
            position: relative;
            transition: 0.2s;
        }
        
        .preview-item:hover {
            transform: translateY(-3px);
            border-color: #1e4a7a;
        }
        
        .preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .remove-btn {
            position: absolute;
            top: 8px;
            right: 8px;
            background: #ff4757;
            color: white;
            border: none;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.2s;
        }
        
        .remove-btn:hover {
            background: #e03a4a;
            transform: scale(1.1);
        }
        
        .char-count {
            font-size: 12px;
            color: #64748b;
            text-align: right;
            margin-top: 6px;
        }
        
        .submit-btn {
            background: linear-gradient(135deg, #1e4a7a, #0b3b6b);
            color: white;
            border: none;
            padding: 16px 40px;
            border-radius: 60px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            width: 100%;
            transition: 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }
        
        .submit-btn:hover {
            background: linear-gradient(135deg, #0b3b6b, #072c4a);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(30,74,122,0.3);
        }
        
        .status-message {
            padding: 16px 20px;
            border-radius: 20px;
            margin-bottom: 25px;
            font-weight: 600;
            animation: slideDown 0.4s ease;
        }
        
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-15px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .status-success {
            background: #dcfce7;
            color: #15803d;
            border-left: 4px solid #22c55e;
        }
        
        .status-error {
            background: #fee2e2;
            color: #b91c1c;
            border-left: 4px solid #ef4444;
        }
        
        .required {
            color: #ef4444;
        }
        
        /* Sidebar */
        .sidebar {
            background: #fff;
            border-radius: 32px;
            padding: 25px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.08);
            border: 1px solid #e2e8f0;
            height: fit-content;
            position: sticky;
            top: 20px;
        }
        
        .sidebar-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #1e2a3a;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 2px solid #eef2f6;
            padding-bottom: 12px;
        }
        
        .stats-box {
            background: #f8fafc;
            border-radius: 24px;
            padding: 20px;
            margin-bottom: 25px;
        }
        
        .stat-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding-bottom: 12px;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .stat-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .stat-label {
            color: #475569;
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .stat-value {
            font-weight: 700;
            color: #1e4a7a;
            font-size: 1.3rem;
        }
        
        .admin-login-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            background: linear-gradient(135deg, #1e4a7a, #0b3b6b);
            color: white;
            text-align: center;
            padding: 12px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 25px;
            transition: 0.2s;
        }
        
        .admin-login-btn:hover {
            background: linear-gradient(135deg, #0b3b6b, #072c4a);
            transform: translateY(-2px);
        }
        
        .recent-reports {
            margin-top: 10px;
        }
        
        .report-item {
            background: #f8fafc;
            border-radius: 20px;
            padding: 15px;
            margin-bottom: 15px;
            border-left: 4px solid #1e4a7a;
            transition: 0.2s;
        }
        
        .report-item:hover {
            transform: translateX(5px);
            background: #f1f5f9;
        }
        
        .report-item h4 {
            font-size: 1rem;
            font-weight: 700;
            color: #1e2a3a;
            margin-bottom: 6px;
        }
        
        .report-item p {
            font-size: 0.85rem;
            color: #475569;
            line-height: 1.4;
        }
        
        .report-status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 30px;
            font-size: 11px;
            font-weight: 600;
            margin-top: 8px;
        }
        
        .status-approved {
            background: #dcfce7;
            color: #15803d;
        }
        
        /* Footer */
        .footer {
            background: #2d3748;
            color: #cbd5e0;
            text-align: center;
            padding: 25px 15px;
            margin-top: 40px;
            font-size: 14px;
            border-top: 1px solid #4a5568;
        }
        
        .footer a {
            color: #90cdf4;
            text-decoration: none;
            margin: 0 12px;
            font-weight: 500;
            transition: 0.2s;
        }
        
        .footer a:hover {
            color: #fff;
            text-decoration: underline;
        }
        
        .footer .separator {
            color: #718096;
            margin: 0 4px;
        }
        
        .footer .copyright {
            margin-top: 12px;
            font-size: 12px;
            color: #a0aec0;
        }
        
        /* Mobile */
        @media (max-width: 1024px) {
            .container {
                grid-template-columns: 1fr;
            }
            .sidebar {
                position: static;
                order: -1;
            }
        }
        
        @media (max-width: 768px) {
            .container {
                margin: 20px auto;
                padding: 0 16px;
            }
            .form-container {
                padding: 20px;
            }
            .section-title {
                font-size: 1.2rem;
            }
            .preview-item {
                width: 100px;
                height: 100px;
            }
        }
    </style>
</head>
<body>

<!-- HEADER (Blue theme) -->
<div class="header">
    <div class="logo">
        <img src="MART72.JPG" alt="GSM Mart 72 Logo">
    </div>
    <h1><i class="fas fa-shield-alt"></i> Scammer Reporting System</h1>
    <div class="subtitle">Report scammers and help protect others from fraud</div>
    <div class="nav-links">
        <a href="index.php"><i class="fas fa-home"></i> Home</a>
        <a href="reports.php"><i class="fas fa-list"></i> View Reports</a>
        <a href="website.php"><i class="fas fa-lock"></i> Visit Website</a>
        <a href="#how-it-works"><i class="fas fa-question-circle"></i> How it Works</a>
    </div>
</div>

<div class="container">
    <div class="main-content">
        <div class="form-container">
            <?php if (isset($success)): ?>
                <div class="status-message status-success">
                    <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                    <p style="margin-top: 8px; font-size: 13px;">Your report will be reviewed by admin within 24 hours.</p>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="status-message status-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data" id="reportForm">
                <!-- Section 1: About You -->
                <div class="form-section">
                    <div class="section-title">
                        <i class="fas fa-user"></i>
                        <span>About You <span class="required">*</span></span>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Your Full Name</label>
                            <div class="input-icon"><i class="fas fa-user"></i></div>
                            <input type="text" name="user_name" required placeholder="Enter your full name">
                        </div>
                        <div class="form-group">
                            <label>Your Mobile Number</label>
                            <div class="input-icon"><i class="fas fa-mobile-alt"></i></div>
                            <input type="tel" name="user_mobile" required placeholder="Enter mobile number">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Your Email Address</label>
                        <div class="input-icon"><i class="fas fa-envelope"></i></div>
                        <input type="email" name="user_email" required placeholder="Enter your email address">
                    </div>
                </div>
                
                <!-- Section 2: About Scammer -->
                <div class="form-section">
                    <div class="section-title">
                        <i class="fas fa-user-slash"></i>
                        <span>About Scammer <span class="required">*</span></span>
                    </div>
                    
                    <div class="form-group">
                        <label>Scammer Name/Nickname</label>
                        <div class="input-icon"><i class="fas fa-user-ninja"></i></div>
                        <input type="text" name="scammer_name" required placeholder="Enter scammer's name or nickname">
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Scammer Mobile Number</label>
                            <div class="input-icon"><i class="fas fa-phone"></i></div>
                            <input type="tel" name="scammer_mobile" required placeholder="Scammer's mobile number">
                        </div>
                        <div class="form-group">
                            <label>Scammer WhatsApp Number</label>
                            <div class="input-icon"><i class="fab fa-whatsapp"></i></div>
                            <input type="tel" name="scammer_whatsapp" placeholder="If different">
                        </div>
                    </div>
                </div>
                
                <!-- Section 3: Description -->
                <div class="form-section">
                    <div class="section-title">
                        <i class="fas fa-file-alt"></i>
                        <span>Description <span class="required">*</span></span>
                    </div>
                    
                    <div class="form-group">
                        <label>Short Summary</label>
                        <div class="input-icon"><i class="fas fa-heading"></i></div>
                        <input type="text" name="short_description" maxlength="200" required placeholder="Brief summary (max 200 characters)">
                        <div class="char-count"><span id="shortCount">0</span>/200 characters</div>
                    </div>
                    
                    <div class="form-group">
                        <label>Full Story</label>
                        <div class="input-icon" style="top: 45px;"><i class="fas fa-file-alt"></i></div>
                        <textarea name="long_description" required placeholder="Describe the scam in detail..."></textarea>
                    </div>
                </div>
                
                <!-- Section 4: Image Uploads -->
                <div class="form-section">
                    <div class="section-title">
                        <i class="fas fa-camera"></i>
                        <span>Upload Evidence (Screenshots)</span>
                    </div>
                    
                    <p style="color: #475569; margin-bottom: 20px; font-size: 14px;">
                        <i class="fas fa-info-circle"></i> Upload screenshots of chat, transaction proof, or any other evidence
                    </p>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Evidence Image 1</label>
                            <div class="upload-area" id="uploadArea1">
                                <div class="upload-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                                <div class="upload-text">Drag & drop or click to upload</div>
                                <input type="file" name="image1[]" id="image1" multiple style="display: none;">
                                <button type="button" class="upload-btn" onclick="document.getElementById('image1').click()">
                                    <i class="fas fa-folder-open"></i> Choose File
                                </button>
                            </div>
                            <div id="preview1" class="image-preview"></div>
                        </div>
                        
                        <div class="form-group">
                            <label>Evidence Image 2</label>
                            <div class="upload-area" id="uploadArea2">
                                <div class="upload-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                                <div class="upload-text">Drag & drop or click to upload</div>
                                <input type="file" name="image2[]" id="image2" multiple style="display: none;">
                                <button type="button" class="upload-btn" onclick="document.getElementById('image2').click()">
                                    <i class="fas fa-folder-open"></i> Choose File
                                </button>
                            </div>
                            <div id="preview2" class="image-preview"></div>
                        </div>
                    </div>
                </div>
                
                <!-- Terms -->
                <div class="form-section">
                    <div class="form-group">
                        <label style="display: flex; align-items: flex-start; gap: 10px;">
                            <input type="checkbox" name="terms" required style="width: auto; margin-top: 3px;">
                            <span>I confirm that all information provided is accurate to the best of my knowledge. I understand that false reporting may lead to legal action. <span class="required">*</span></span>
                        </label>
                    </div>
                </div>
                
                <button type="submit" class="submit-btn">
                    <i class="fas fa-paper-plane"></i> Submit Report
                </button>
                
                <p style="text-align: center; margin-top: 20px; color: #64748b; font-size: 13px;">
                    <i class="fas fa-shield-alt"></i> Your information is secure and will only be used for verification purposes.
                </p>
            </form>
        </div>
    </div>
    
    <!-- Sidebar -->
    <div class="sidebar">
        <h2 class="sidebar-title"><i class="fas fa-chart-bar"></i> Live Statistics</h2>
        
        <?php
        $conn = db_connect();
        $total = $conn->query("SELECT COUNT(*) as total FROM reports")->fetch_assoc()['total'];
        $approved = $conn->query("SELECT COUNT(*) as approved FROM reports WHERE status='approved'")->fetch_assoc()['approved'];
        $pending = $conn->query("SELECT COUNT(*) as pending FROM reports WHERE status='pending'")->fetch_assoc()['pending'];
        $today = $conn->query("SELECT COUNT(*) as today FROM reports WHERE DATE(report_date) = CURDATE()")->fetch_assoc()['today'];
        ?>
        
        <div class="stats-box">
            <div class="stat-item"><span class="stat-label">Total Reports</span><span class="stat-value"><?php echo $total; ?></span></div>
            <div class="stat-item"><span class="stat-label">Verified Reports</span><span class="stat-value" style="color: #22c55e;"><?php echo $approved; ?></span></div>
            <div class="stat-item"><span class="stat-label">Pending Review</span><span class="stat-value" style="color: #f59e0b;"><?php echo $pending; ?></span></div>
            <div class="stat-item"><span class="stat-label">Today's Reports</span><span class="stat-value" style="color: #3b82f6;"><?php echo $today; ?></span></div>
        </div>
        
        <a href="admin.php" class="admin-login-btn">
            <i class="fas fa-lock"></i> Admin Login Panel
        </a>
        
        <div class="recent-reports">
            <h2 class="sidebar-title"><i class="fas fa-history"></i> Recent Verified</h2>
            <?php
            $result = $conn->query("SELECT scammer_name, short_description FROM reports WHERE status='approved' ORDER BY report_date DESC LIMIT 3");
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="report-item">';
                    echo '<h4>' . htmlspecialchars($row['scammer_name']) . '</h4>';
                    echo '<p>' . htmlspecialchars(substr($row['short_description'], 0, 70)) . '...</p>';
                    echo '<span class="report-status status-approved">✅ Verified</span>';
                    echo '</div>';
                }
            } else {
                echo '<p style="text-align:center; padding:20px;">No verified reports yet</p>';
            }
            $conn->close();
            ?>
            <a href="reports.php" style="display: block; text-align: center; margin-top: 15px; color: #1e4a7a; text-decoration: none; font-weight: 600;">
                <i class="fas fa-external-link-alt"></i> View All Reports
            </a>
        </div>
        
        <div style="margin-top: 25px; padding: 18px; background: #f8fafc; border-radius: 20px; border-left: 4px solid #1e4a7a;">
            <h3 style="font-size: 1rem; margin-bottom: 10px; color: #1e2a3a;"><i class="fas fa-lightbulb"></i> Safety Tips</h3>
            <ul style="font-size: 13px; color: #475569; padding-left: 20px; line-height: 1.6;">
                <li>Never share OTP or password</li>
                <li>Verify before sending money</li>
                <li>Check official websites</li>
                <li>Report suspicious activity</li>
            </ul>
        </div>
    </div>
</div>

<!-- FOOTER (Same as main) -->
<div class="footer">
    <div>
        <a href="index.php#list">📋 Reports</a>
        <span class="separator">|</span>
        <a href="about.php">ℹ️ About</a>
        <span class="separator">|</span>
        <a href="content.php">📄 Content</a>
    </div>
    <div class="copyright">
        Powered by <strong>SD Software</strong>
    </div>
    <div class="copyright">
        © <?php echo date('Y'); ?> mart72.top — Verified Scammer Reports
    </div>
</div>

<script>
    // Character counter
    const shortInput = document.querySelector('input[name="short_description"]');
    const shortCount = document.getElementById('shortCount');
    if (shortInput && shortCount) {
        shortInput.addEventListener('input', function() {
            shortCount.textContent = this.value.length;
        });
    }
    
    // File upload handlers
    function setupUpload(uploadAreaId, inputId, previewId) {
        const area = document.getElementById(uploadAreaId);
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        if (!area || !input || !preview) return;
        
        area.onclick = () => input.click();
        area.addEventListener('dragover', e => { e.preventDefault(); area.classList.add('dragover'); });
        area.addEventListener('dragleave', () => area.classList.remove('dragover'));
        area.addEventListener('drop', e => {
            e.preventDefault();
            area.classList.remove('dragover');
            const files = Array.from(input.files).concat(Array.from(e.dataTransfer.files));
            updateFiles(files);
        });
        input.addEventListener('change', () => updateFiles(Array.from(input.files)));
        
        function updateFiles(files) {
            const dt = new DataTransfer();
            files.forEach(f => dt.items.add(f));
            input.files = dt.files;
            render();
        }
        
        function render() {
            preview.innerHTML = '';
            Array.from(input.files).forEach((file, idx) => {
                const div = document.createElement('div');
                div.className = 'preview-item';
                if (file.type.startsWith('image/')) {
                    div.innerHTML = `<img src="${URL.createObjectURL(file)}"><button type="button" class="remove-btn" onclick="removeFile('${inputId}', ${idx}, '${previewId}')"><i class="fas fa-times"></i></button>`;
                } else {
                    div.innerHTML = `<div style="padding:10px;">${file.name}</div><button type="button" class="remove-btn" onclick="removeFile('${inputId}', ${idx}, '${previewId}')"><i class="fas fa-times"></i></button>`;
                }
                preview.appendChild(div);
            });
        }
    }
    
    function removeFile(inputId, index, previewId) {
        const input = document.getElementById(inputId);
        const files = Array.from(input.files);
        files.splice(index, 1);
        const dt = new DataTransfer();
        files.forEach(f => dt.items.add(f));
        input.files = dt.files;
        document.getElementById(previewId).innerHTML = '';
        // re-render manually
        const evt = new Event('change');
        input.dispatchEvent(evt);
    }
    
    setupUpload('uploadArea1', 'image1', 'preview1');
    setupUpload('uploadArea2', 'image2', 'preview2');
</script>
</body>
</html>