<?php
/* ================= SHOW ERRORS ================= */
error_reporting(E_ALL);
ini_set('display_errors', 1);

/* ================= DB CONNECTION ================= */
$conn = new mysqli(
    "localhost",
    "marttop_scammer",
    "saeed@saif1122",
    "marttop_scammer"
);
if($conn->connect_error){
    die("Database Connection Failed");
}
?>
<!DOCTYPE html>
<html lang="ur" dir="rtl">
<head>
<meta charset="UTF-8">
<title>About Us | GSM Mart 72 – Scammer Awareness</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">

<!-- Fonts -->
<link href="https://fonts.googleapis.com/earlyaccess/notonastaliqurdudraft.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

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
    line-height: 1.6;
}

h1, h2, h3, h4, .logo, .nav-links a, .group-links a, .footer a {
    font-family: 'Poppins', sans-serif;
}

/* For Urdu text */
.urdu-text, p, .info-box p, .group-links a {
    font-family: 'Noto Nastaliq Urdu Draft', 'Inter', sans-serif;
}

/* ================= HEADER (Same as main) ================= */
.header {
    background: linear-gradient(135deg, #0b3b6b, #1e4a7a);
    color: #fff;
    text-align: center;
    padding: 20px 15px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.2);
}

.logo {
    display: flex;
    justify-content: center;
    margin-bottom: 12px;
}

.logo img {
    width: 90px;
    height: auto;
    padding: 6px;
    background: #fff;
    border-radius: 16px;
    border: 2px solid #6ea8fe;
    box-shadow: 0 6px 20px rgba(0,0,0,0.25);
    transition: 0.3s ease;
}

.logo img:hover {
    transform: scale(1.05);
    border-color: #ffffff;
}

.tagline {
    font-size: 14px;
    margin-top: 6px;
    opacity: 0.9;
    font-family: 'Poppins', sans-serif;
}

/* Navigation links (optional, keep if needed) */
.nav-links {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 12px;
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
    max-width: 1000px;
    margin: 30px auto;
    padding: 0 20px;
}

/* ================= CARDS ================= */
.card {
    background: #fff;
    border-radius: 28px;
    overflow: hidden;
    box-shadow: 0 12px 28px rgba(0,0,0,0.08);
    border: 1px solid #e2e8f0;
    margin-bottom: 30px;
    transition: 0.2s;
}

.card-body {
    padding: 28px 24px;
}

/* Headings inside cards */
h2 {
    color: #1e4a7a;
    font-size: 1.6rem;
    margin-bottom: 16px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 10px;
    border-right: 4px solid #1e4a7a;
    padding-right: 15px;
}

h2 i {
    color: #1e4a7a;
}

p {
    font-size: 1rem;
    line-height: 1.8;
    margin-bottom: 15px;
    color: #2d3e50;
}

/* Info box (like warning or highlight) */
.info-box {
    background: #f8fafc;
    border-right: 5px solid #1e4a7a;
    padding: 20px;
    border-radius: 20px;
    margin: 20px 0;
}

/* Group links */
.group-links {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-top: 15px;
}
.group-links a {
    display: block;
    background: linear-gradient(135deg, #1e4a7a, #0b3b6b);
    color: #fff;
    text-decoration: none;
    padding: 14px 20px;
    border-radius: 50px;
    text-align: center;
    font-weight: 600;
    transition: 0.2s;
    font-size: 1rem;
}
.group-links a:hover {
    background: linear-gradient(135deg, #0b3b6b, #072c4a);
    transform: translateX(-5px);
}

/* Sponsors grid */
.sponsor-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 24px;
    margin-top: 20px;
}
.sponsor-card {
    position: relative;
    background: #fff;
    border-radius: 24px;
    padding: 25px 15px;
    text-align: center;
    text-decoration: none;
    color: #1e2a3a;
    box-shadow: 0 10px 20px rgba(0,0,0,0.05);
    transition: 0.25s;
    border: 1px solid #e2e8f0;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
}
.sponsor-card:hover {
    transform: translateY(-6px);
    border-color: #1e4a7a;
    box-shadow: 0 20px 30px rgba(0,0,0,0.1);
}
.sponsor-card img {
    max-width: 180px;
    max-height: 140px;
    object-fit: contain;
    border-radius: 16px;
}
.sponsor-name {
    font-weight: 700;
    font-size: 1.1rem;
    color: #1e4a7a;
}
.sponsored-badge {
    position: absolute;
    top: 12px;
    left: -30px; /* RTL adjustment */
    background: #ff9800;
    color: #fff;
    font-size: 11px;
    font-weight: 700;
    padding: 4px 40px;
    transform: rotate(-45deg);
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    letter-spacing: 1px;
}

/* ================= FLOATING WHATSAPP ================= */
.whatsapp {
    position: fixed;
    bottom: 20px;
    left: 20px;
    width: 56px;
    height: 56px;
    background: #25D366;
    color: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 30px;
    text-decoration: none;
    z-index: 1000;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(37, 211, 102, 0.3);
}
.whatsapp:hover {
    transform: scale(1.1) rotate(5deg);
    box-shadow: 0 8px 25px rgba(37, 211, 102, 0.5);
}

/* ================= FOOTER (Same as main) ================= */
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
.footer-logo img {
    max-width: 80px;
    margin-bottom: 10px;
    background: #fff;
    border-radius: 12px;
    padding: 5px;
}

/* ================= MOBILE ================= */
@media (max-width: 768px) {
    .container {
        margin: 20px auto;
        padding: 0 16px;
    }
    .card-body {
        padding: 20px 16px;
    }
    h2 {
        font-size: 1.3rem;
    }
    p {
        font-size: 0.95rem;
    }
    .group-links a {
        padding: 12px 16px;
        font-size: 0.9rem;
    }
    .sponsor-grid {
        gap: 16px;
    }
    .sponsor-card img {
        max-width: 140px;
    }
    .footer a {
        margin: 0 8px;
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
    <div class="tagline">
        Public Awareness & Scam Prevention Platform
    </div>
    <!-- Optional navigation (same as main page) -->
    <div class="nav-links">
        <a href="reports.php">➕ Report</a>
        <a href="index.php#list">📋 Reports</a>
        <a href="about.php">🔒 About Us</a>
    </div>
</div>

<div class="container">
    <!-- Main About Card -->
    <div class="card">
        <div class="card-body">
            <h2><i class="fas fa-info-circle"></i> ہمارے بارے میں</h2>
            <p>GSM Mart 72 ایک قابل اعتماد پلیٹ فارم ہے جو محفوظ اور شفاف موبائل بزنس کے لیے بنایا گیا ہے۔ ہم عوام کو اسکیموں اور فراڈ سے بچانے کے لیے پرعزم ہیں۔ تمام رپورٹس کی تصدیق کے بعد شائع کی جاتی ہیں۔</p>
            
            <div class="info-box">
                <h2><i class="fas fa-handshake"></i> خرید و فروخت</h2>
                <p>تمام لین دین ایڈمن کی نگرانی میں ہوتا ہے۔ براہ کرم مکمل تصدیق کے بعد لین دین کریں۔ اگر آپ کو کسی بھی قسم کا شبہ ہو تو فوری طور پر ہمیں رپورٹ کریں۔</p>
            </div>
        </div>
    </div>

    <!-- Group Links Card -->
    <div class="card">
        <div class="card-body">
            <h2><i class="fab fa-whatsapp"></i> ہمارے گروپ لنکس جوائن کریں</h2>
            <p>تازہ ترین اپڈیٹس، آفرز اور مارکیٹ ریٹس کے لیے ہمارے واٹس ایپ گروپس جوائن کریں۔</p>
            <div class="group-links">
                <a href="https://chat.whatsapp.com/LET1zMkZs7mExFNkCsahSm" target="_blank"><i class="fab fa-whatsapp"></i> Admin Only Group</a>
                <a href="https://chat.whatsapp.com/KiNLwa9b2VV2RveWILBSsJ" target="_blank"><i class="fab fa-whatsapp"></i> GSM Mart72 Group 1</a>
                <a href="https://chat.whatsapp.com/JT8LdYTy9otA0CvPgf7meJ" target="_blank"><i class="fab fa-whatsapp"></i> GSM Mart72 Group 2</a>
                <a href="https://chat.whatsapp.com/GITiDuC7CV5CbrMdsOqxG3" target="_blank"><i class="fab fa-whatsapp"></i> GSM Mart72 Group 3</a>
            </div>
        </div>
    </div>

    <!-- Sponsors Card -->
    <div class="card">
        <div class="card-body">
            <h2><i class="fas fa-trophy"></i> ہمارے اسپانسرز</h2>
            <div class="sponsor-grid">
            <?php
            $q = $conn->query("SELECT * FROM sponsors WHERE status=1 ORDER BY id DESC");
            if($q && $q->num_rows > 0){
                while($s = $q->fetch_assoc()){
            ?>
                <a href="<?= htmlspecialchars($s['website']); ?>" target="_blank" class="sponsor-card">
                    <div class="sponsored-badge">SPONSORED</div>
                    <img src="uploads/<?= htmlspecialchars($s['image']); ?>" alt="<?= htmlspecialchars($s['name']); ?>">
                    <div class="sponsor-name"><?= htmlspecialchars($s['name']); ?></div>
                </a>
            <?php
                }
            } else {
                echo "<p style='text-align:center; grid-column:1/-1;'>کوئی اسپانسر دستیاب نہیں</p>";
            }
            ?>
            </div>
        </div>
    </div>
</div>

<!-- Floating WhatsApp Button -->
<a href="https://wa.me/923241473537" class="whatsapp" target="_blank">
    <i class="fab fa-whatsapp"></i>
</a>

<!-- FOOTER (Same as main page) -->
<div class="footer">
    <div class="footer-logo">
        <img src="MART72.JPG" alt="GSM Mart 72 Logo">
    </div>
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
        © <?= date('Y') ?> mart72.top — Verified Scammer Reports
    </div>
</div>

<?php $conn->close(); ?>
</body>
</html>