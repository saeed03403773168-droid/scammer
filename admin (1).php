<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'marttop_scammer');
define('DB_PASS', 'saeed@saif1122');
define('DB_NAME', 'marttop_scammer');

// Database connection with error handling
function db_connect() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        show_setup_error($conn->connect_error);
        exit;
    }
    if (!table_exists($conn, 'admins')) {
        show_setup_error("Database tables are not setup. Please run setup.php first.");
        exit;
    }
    return $conn;
}

// Check if table exists
function table_exists($conn, $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    return $result->num_rows > 0;
}

// Show setup error
function show_setup_error($error_msg) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Setup Required</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            body{margin:0;font-family:'Segoe UI',sans-serif;background:linear-gradient(135deg,#667eea,#764ba2);display:flex;justify-content:center;align-items:center;height:100vh;}
            .error-container{background:#fff;border-radius:20px;padding:30px;width:90%;max-width:600px;text-align:center;box-shadow:0 20px 60px rgba(0,0,0,0.3);}
            .error-container h1{color:#ff4757;margin-bottom:15px;}
            .error-container p{margin-bottom:25px;color:#2d3436;}
            .btn{padding:12px 20px;border:none;border-radius:10px;color:#fff;font-weight:600;margin:5px;text-decoration:none;display:inline-block;}
            .btn-primary{background:#2ed573;}
            .btn-secondary{background:#636e72;}
        </style>
    </head>
    <body>
        <div class="error-container">
            <h1><i class="fas fa-exclamation-triangle"></i> Setup Required</h1>
            <p><?php echo $error_msg; ?></p>
            <a href="setup.php" class="btn btn-primary"><i class="fas fa-cogs"></i> Run Setup</a>
            <a href="index.php" class="btn btn-secondary"><i class="fas fa-home"></i> Back Home</a>
            <p style="margin-top:20px;font-size:14px;">Default admin: <strong>admin / admin@nadeem72</strong></p>
        </div>
    </body>
    </html>
    <?php exit;
}

// ---------------- LOGIN ----------------
if (!isset($_SESSION['admin_logged_in'])) {
    $error='';
    if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['login'])){
        $conn = db_connect();
        $username = $conn->real_escape_string($_POST['username']);
        $password = $_POST['password'];
        $result = $conn->query("SELECT * FROM admins WHERE username='$username'");
        if($result->num_rows>0){
            $admin=$result->fetch_assoc();
            if(password_verify($password,$admin['password'])){
                $_SESSION['admin_logged_in']=true;
                $_SESSION['admin_id']=$admin['id'];
                $_SESSION['admin_username']=$admin['username'];
                header('Location: admin.php');
                exit;
            }else{$error="Invalid password!";}
        }else{$error="User not found!";}
        $conn->close();
    }
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Admin Login</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            body{margin:0;font-family:'Segoe UI',sans-serif;background:linear-gradient(135deg,#667eea,#764ba2);display:flex;justify-content:center;align-items:center;height:100vh;}
            .login-container{background:#fff;padding:30px;border-radius:20px;width:90%;max-width:400px;box-shadow:0 20px 60px rgba(0,0,0,0.3);}
            .login-container h1{color:#ff4757;text-align:center;margin-bottom:20px;}
            .form-group{margin-bottom:20px;}
            .form-group input{width:100%;padding:12px 15px;border-radius:10px;border:2px solid #dfe4ea;}
            .login-btn{width:100%;padding:12px;background:#2ed573;color:#fff;border:none;border-radius:10px;font-weight:600;cursor:pointer;}
            .login-btn:hover{opacity:.9;}
            .error-message{background:#f8d7da;color:#721c24;padding:10px;border-radius:10px;margin-bottom:15px;}
        </style>
    </head>
    <body>
        <div class="login-container">
            <h1><i class="fas fa-lock"></i> Admin Login</h1>
            <?php if($error): ?><div class="error-message"><?php echo $error;?></div><?php endif;?>
            <form method="POST">
                <input type="hidden" name="login" value="1">
                <div class="form-group">
                    <input type="text" name="username" required placeholder="Username">
                </div>
                <div class="form-group">
                    <input type="password" name="password" required placeholder="Password">
                </div>
                <button type="submit" class="login-btn"><i class="fas fa-sign-in-alt"></i> Login</button>
            </form>
        </div>
    </body>
    </html>
    <?php exit;
}

// ---------------- LOGOUT ----------------
if(isset($_GET['logout'])){
    session_destroy();
    header('Location: admin.php');
    exit;
}

// ---------------- ADMIN PANEL ----------------
$conn=db_connect();
$message='';

// Handle actions
if(isset($_GET['action'],$_GET['id'])){
    $id=intval($_GET['id']);
    $admin_name=$_SESSION['admin_username'];
    switch($_GET['action']){
        case 'approve':
            $conn->query("UPDATE reports SET status='approved',approved_date=NOW(),approved_by='$admin_name' WHERE id=$id");
            $message='<div style="color:#2ed573;margin-bottom:15px;">Report approved!</div>';
            break;
        case 'reject':
            $conn->query("UPDATE reports SET status='rejected',approved_date=NOW(),approved_by='$admin_name' WHERE id=$id");
            $message='<div style="color:#e74c3c;margin-bottom:15px;">Report rejected!</div>';
            break;
        case 'delete':
            $conn->query("DELETE FROM reports WHERE id=$id");
            $message='<div style="color:#ff4757;margin-bottom:15px;">Report deleted!</div>';
            break;
    }
}

// Filter + search
$filter = $_GET['filter']??'all';
$status_filter='';
switch($filter){
    case 'pending': $status_filter="WHERE status='pending'";break;
    case 'approved': $status_filter="WHERE status='approved'";break;
    case 'rejected': $status_filter="WHERE status='rejected'";break;
    default: $status_filter='';break;
}
$search=$_GET['search']??'';
$search_filter='';
if($search){
    $s=$conn->real_escape_string($search);
    $search_filter=$status_filter?" AND (scammer_name LIKE '%$s%' OR scammer_mobile LIKE '%$s%' OR user_name LIKE '%$s%')":" WHERE (scammer_name LIKE '%$s%' OR scammer_mobile LIKE '%$s%' OR user_name LIKE '%$s%')";
}

// Stats
$stats=$conn->query("SELECT COUNT(*) as total,SUM(status='approved') as approved,SUM(status='pending') as pending,SUM(status='rejected') as rejected FROM reports")->fetch_assoc();
$reports_result=$conn->query("SELECT * FROM reports $status_filter $search_filter ORDER BY report_date DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Segoe UI',sans-serif;background:#f4f6fb;color:#2d3436;}
        .admin-header{position:sticky;top:0;z-index:1000;background:linear-gradient(135deg,#667eea,#764ba2);padding:15px 20px;display:flex;justify-content:space-between;align-items:center;color:#fff;}
        .logout-btn{background:#ff4757;padding:10px 15px;border-radius:8px;color:#fff;text-decoration:none;font-weight:600;}
        .admin-container{display:flex;min-height:calc(100vh - 70px);}
        .admin-sidebar{width:260px;background:#1e272e;padding:20px 0;}
        .sidebar-menu{list-style:none;}
        .menu-item{display:flex;align-items:center;gap:12px;padding:14px 20px;color:#dfe6e9;text-decoration:none;font-weight:500;}
        .menu-item:hover,.menu-item.active{background:#3742fa;color:#fff;}
        .admin-content{flex:1;padding:20px;}
        .stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:15px;margin-bottom:25px;}
        .stat-card{background:#fff;border-radius:15px;padding:20px;box-shadow:0 10px 30px rgba(0,0,0,.08);}
        .stat-number{font-size:28px;font-weight:700;}
        .stat-label{color:#636e72;}
        .filter-row{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:20px;}
        .search-box{flex:1;position:relative;}
        .search-box input{width:100%;padding:14px 45px;border-radius:12px;border:2px solid #dfe6e9;font-size:15px;}
        .search-icon{position:absolute;left:15px;top:50%;transform:translateY(-50%);color:#636e72;}
        .reports-table{width:100%;border-collapse:collapse;background:#fff;border-radius:15px;overflow:hidden;}
        .reports-table th{background:#f1f2f6;padding:14px;text-align:left;}
        .reports-table td{padding:14px;border-bottom:1px solid #f1f2f6;}
        .action-btn{padding:8px 12px;border-radius:8px;border:none;cursor:pointer;font-weight:600;margin:3px;}
        .btn-view{background:#0984e3;color:#fff;}
        .btn-approve{background:#2ed573;color:#fff;}
        .btn-reject{background:#ffa502;color:#fff;}
        .btn-delete{background:#ff4757;color:#fff;}
        .status-badge{padding:6px 12px;border-radius:20px;font-size:13px;font-weight:600;}
        .status-approved{background:#d4f8e8;color:#2ed573;}
        .status-pending{background:#fff3cd;color:#f39c12;}
        .status-rejected{background:#f8d7da;color:#e74c3c;}
        @media(max-width:900px){
            .admin-container{flex-direction:column;}
            .reports-table thead{display:none;}
            .reports-table tr{display:block;margin:15px;border-radius:15px;box-shadow:0 8px 25px rgba(0,0,0,.08);}
            .reports-table td{display:block;border:none;}
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <h1><i class="fas fa-shield-alt"></i> Admin Panel</h1>
        <a href="admin.php?logout=1" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
    <div class="admin-container">
        <div class="admin-sidebar">
            <ul class="sidebar-menu">
                <li><a href="admin.php?filter=all" class="menu-item <?php echo $filter=='all'?'active':'';?>"><i class="fas fa-list"></i> All Reports</a></li>
                <li><a href="admin.php?filter=pending" class="menu-item <?php echo $filter=='pending'?'active':'';?>"><i class="fas fa-clock"></i> Pending</a></li>
                <li><a href="admin.php?filter=approved" class="menu-item <?php echo $filter=='approved'?'active':'';?>"><i class="fas fa-check-circle"></i> Approved</a></li>
                <li><a href="admin.php?filter=rejected" class="menu-item <?php echo $filter=='rejected'?'active':'';?>"><i class="fas fa-times-circle"></i> Rejected</a></li>
            </ul>
        </div>
        <div class="admin-content">
            <?php echo $message;?>
            <div class="stats-grid">
                <div class="stat-card"><div class="stat-number"><?php echo $stats['total'];?></div><div class="stat-label">Total</div></div>
                <div class="stat-card"><div class="stat-number"><?php echo $stats['approved'];?></div><div class="stat-label">Approved</div></div>
                <div class="stat-card"><div class="stat-number"><?php echo $stats['pending'];?></div><div class="stat-label">Pending</div></div>
                <div class="stat-card"><div class="stat-number"><?php echo $stats['rejected'];?></div><div class="stat-label">Rejected</div></div>
            </div>
            <div class="filter-row">
                <form class="search-box" onsubmit="event.preventDefault();window.location='admin.php?filter=<?php echo $filter;?>&search='+encodeURIComponent(this.search.value)">
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search);?>" placeholder="Search...">
                    <div class="search-icon"><i class="fas fa-search"></i></div>
                </form>
            </div>
            <?php if($reports_result->num_rows>0):?>
            <table class="reports-table">
                <thead><tr><th>ID</th><th>Reporter</th><th>Scammer</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
                <tbody>
                <?php while($row=$reports_result->fetch_assoc()):?>
                    <tr>
                        <td>#<?php echo $row['id'];?></td>
                        <td><?php echo htmlspecialchars($row['user_name']);?></td>
                        <td><?php echo htmlspecialchars($row['scammer_name']);?></td>
                        <td><span class="status-badge status-<?php echo $row['status'];?>"><?php echo ucfirst($row['status']);?></span></td>
                        <td><?php echo date('M d, Y',strtotime($row['report_date']));?></td>
                        <td>
         <button class="action-btn btn-view"
onclick="window.location.href='view_report.php?id=<?php echo $row['id']; ?>'">
View
</button>

                            <?php if($row['status']=='pending'):?>
                            <button class="action-btn btn-approve" onclick="window.location='admin.php?action=approve&id=<?php echo $row['id'];?>&filter=<?php echo $filter;?>'">Approve</button>
                            <button class="action-btn btn-reject" onclick="window.location='admin.php?action=reject&id=<?php echo $row['id'];?>&filter=<?php echo $filter;?>'">Reject</button>
                            <?php endif;?>
                            <button class="action-btn btn-delete" onclick="if(confirm('Delete?'))window.location='admin.php?action=delete&id=<?php echo $row['id'];?>&filter=<?php echo $filter;?>'">Delete</button>
                        </td>
                    </tr>
                <?php endwhile;?>
                </tbody>
            </table>
            <?php else:?>
            <p>No reports found.</p>
            <?php endif;?>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>
