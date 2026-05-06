<?php  
session_start();  
error_reporting(E_ALL);  
ini_set('display_errors', 1);  

/* ================= DATABASE CONFIG ================= */  
define('DB_HOST', 'localhost');  
define('DB_USER', 'marttop_scammer');  
define('DB_PASS', 'saeed@saif1122');  
define('DB_NAME', 'marttop_scammer');  

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);  
if ($conn->connect_error) {  
    die("Database connection failed");  
}  

/* ================= SEARCH ================= */  
$search = isset($_GET['search']) ? trim($_GET['search']) : '';  

$sql = "SELECT * FROM reports WHERE status='approved'";  
$params = [];  
if ($search !== '') {  
    $sql .= " AND (scammer_name LIKE ? OR scammer_mobile LIKE ? OR short_description LIKE ?)";  
    $like = "%$search%";  
    $params = [$like, $like, $like];  
}  
$sql .= " ORDER BY approved_date DESC";  

$stmt = $conn->prepare($sql);  
if (!empty($params)) {  
    $stmt->bind_param("sss", ...$params);  
}  
$stmt->execute();  
$result = $stmt->get_result();  

/* ================= STATS ================= */  
$stats = $conn->query("  
    SELECT   
        COUNT(*) total,  
        COUNT(DISTINCT scammer_mobile) unique_scammers,  
        MAX(approved_date) last_verified  
    FROM reports WHERE status='approved'  
")->fetch_assoc();  

/* ================= RECENT REPORTS FOR SLIDER ================= */  
$recent_sql = "SELECT id, scammer_name, scammer_mobile, short_description, image1, approved_date FROM reports WHERE status='approved' ORDER BY approved_date DESC LIMIT 8";  
$recent_result = $conn->query($recent_sql);  
$has_recent = $recent_result && $recent_result->num_rows > 0;  
?>  
<!DOCTYPE html>  
<html lang="en">  
<head>  
<meta charset="UTF-8">  
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes, viewport-fit=cover">  
<title>Verified Scammer Reports | mart72.top</title>  
<!-- Google Fonts -->  
<link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">  
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">  
<style>  
*{margin:0;padding:0;box-sizing:border-box}  

body{  
    font-family: 'Inter', sans-serif;  
    background: #f0f4f8;  
    padding-top:0;  
}  

h1, h2, h3, h4, .logo, .nav-links a, .stat h2, .slider-header h2 {  
    font-family: 'Poppins', sans-serif;  
}  

/* ================= HEADER - STRIKING BLUE ================= */  
.header{  
    position:relative;  
    background: linear-gradient(135deg, #0b3b6b, #1e4a7a);  
    color:#fff;  
    text-align:center;  
    padding:20px 15px;  
    box-shadow:0 6px 20px rgba(0,0,0,0.2);  
}  

.logo{  
    display:flex;  
    justify-content:center;  
    margin-bottom:12px;  
}  

.logo img{  
    width:90px;  
    height:auto;  
    padding:6px;  
    background:#fff;  
    border-radius:16px;  
    border:2px solid #6ea8fe;  
    box-shadow:0 6px 20px rgba(0,0,0,.25);  
    transition:.3s ease;  
}  

.logo img:hover{  
    transform:scale(1.05);  
    border-color: #ffffff;  
}  

.header p{  
    font-size:14px;  
    margin-top:6px;  
    opacity:0.9;  
}  

.nav-links{  
    display:flex;  
    justify-content:center;  
    flex-wrap:wrap;  
    gap:10px;  
    margin-top:12px;  
}  
.nav-links a{  
    color:#fff;  
    text-decoration:none;  
    border:1px solid rgba(255,255,255,.5);  
    padding:6px 14px;  
    border-radius:30px;  
    font-size:13px;  
    font-weight:500;  
    transition:0.2s;  
}  
.nav-links a:hover{ background:#6ea8fe; color:#0b3b6b; border-color:#6ea8fe; }  

/* ================= SLIDER SECTION ================= */  
.slider-section {  
    background: #fff;  
    margin: 20px auto 0;  
    border-radius: 28px;  
    box-shadow: 0 15px 35px rgba(0,0,0,0.08);  
    padding: 20px 15px;  
    max-width: 1300px;  
    width: calc(100% - 30px);  
}  

.slider-header {  
    display: flex;  
    justify-content: space-between;  
    align-items: baseline;  
    flex-wrap: wrap;  
    margin-bottom: 20px;  
    border-left: 5px solid #1e4a7a;  
    padding-left: 15px;  
}  
.slider-header h2 {  
    font-size: 1.6rem;  
    color: #1e2a3a;  
    font-weight: 700;  
}  
.slider-header h2 i {  
    color: #1e4a7a;  
    margin-right: 8px;  
}  
.slider-controls {  
    display: flex;  
    gap: 12px;  
}  
.slider-btn {  
    background: #1e4a7a;  
    border: none;  
    color: #fff;  
    width: 40px;  
    height: 40px;  
    border-radius: 50%;  
    cursor: pointer;  
    font-size: 1.2rem;  
    transition: 0.2s;  
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);  
}  
.slider-btn:hover {  
    background: #0b3b6b;  
    transform: scale(1.05);  
}  

.slider-wrapper {  
    overflow-x: auto;  
    scroll-behavior: smooth;  
    -webkit-overflow-scrolling: touch;  
    scrollbar-width: thin;  
}  
.slider-wrapper::-webkit-scrollbar { height: 6px; }  
.slider-wrapper::-webkit-scrollbar-track { background: #e2e8f0; border-radius: 10px; }  
.slider-wrapper::-webkit-scrollbar-thumb { background: #1e4a7a; border-radius: 10px; }  

.slider-track {  
    display: flex;  
    gap: 20px;  
    padding: 8px 4px 16px 4px;  
}  

.slide-card {  
    flex: 0 0 280px;  
    background: white;  
    border-radius: 24px;  
    overflow: hidden;  
    box-shadow: 0 10px 20px rgba(0,0,0,0.08);  
    transition: transform 0.25s ease, box-shadow 0.2s;  
    text-decoration: none;  
    color: inherit;  
    display: flex;  
    flex-direction: column;  
    border: 1px solid #e2e8f0;  
}  
.slide-card:hover {  
    transform: translateY(-6px);  
    box-shadow: 0 18px 30px rgba(0,0,0,0.12);  
    border-color: #1e4a7a;  
}  

.slide-img {  
    height: 140px;  
    background: #eef2f7;  
    display: flex;  
    align-items: center;  
    justify-content: center;  
    overflow: hidden;  
}  
.slide-img img {  
    width: 100%;  
    height: 100%;  
    object-fit: cover;  
}  
.slide-img .no-img {  
    font-size: 3rem;  
    color: #90a4c3;  
}  

.slide-info { padding: 14px; }  
.slide-info h4 {  
    font-size: 1.1rem;  
    font-weight: 700;  
    color: #1e2a3a;  
    margin-bottom: 6px;  
    white-space: nowrap;  
    overflow: hidden;  
    text-overflow: ellipsis;  
}  
.slide-mobile {  
    font-size: 0.85rem;  
    color: #1e4a7a;  
    font-weight: 600;  
    margin-bottom: 8px;  
}  
.slide-desc {  
    font-size: 0.8rem;  
    color: #4a5568;  
    line-height: 1.4;  
    display: -webkit-box;  
    -webkit-line-clamp: 2;  
    -webkit-box-orient: vertical;  
    overflow: hidden;  
}  
.slide-date {  
    font-size: 0.7rem;  
    color: #94a3b8;  
    margin-top: 8px;  
    border-top: 1px solid #e2e8f0;  
    padding-top: 6px;  
}  

/* ================= CONTAINER ================= */  
.container{ max-width:1200px; margin:0 auto; padding:0 12px; }  

/* ================= STATS ================= */  
.stats{  
    display:grid;  
    grid-template-columns:repeat(auto-fit,minmax(150px,1fr));  
    gap:15px;  
    margin-top:30px;  
}  
.stat{  
    background:#fff;  
    padding:20px 12px;  
    border-radius:24px;  
    text-align:center;  
    box-shadow:0 10px 20px rgba(0,0,0,0.05);  
    border-bottom: 4px solid #1e4a7a;  
    transition: 0.2s;  
}  
.stat:hover { transform: translateY(-3px); border-bottom-color: #0b3b6b; }  
.stat h2{ font-size:28px; color:#1e2a3a; font-weight:800; }  
.stat p{ font-size:13px; color:#4a5568; font-weight:500; }  
.stat i { color: #1e4a7a; margin-right: 6px; }  

/* ================= BEAUTIFUL SEARCH BAR (Mobile friendly) ================= */  
.search-box{  
    position:sticky;  
    top: 10px;  
    z-index:999;  
    background:rgba(255,255,255,.98);  
    backdrop-filter:blur(12px);  
    margin:25px 0;  
    padding:8px;  
    border-radius:60px;  
    display:flex;  
    gap:8px;  
    box-shadow:0 12px 28px rgba(0,0,0,0.08);  
    border:1px solid #cbd5e1;  
}  

.search-box input{  
    flex:1;  
    padding:14px 20px;  
    border-radius:50px;  
    border:1px solid #e2e8f0;  
    font-size:15px;  
    font-family: 'Inter', sans-serif;  
    transition:0.2s;  
    background: #fff;  
}  
.search-box input:focus{  
    outline:none;  
    border-color:#1e4a7a;  
    box-shadow:0 0 0 3px rgba(30,74,122,0.2);  
}  

.search-box button{  
    background: linear-gradient(135deg, #1e4a7a, #0b3b6b);  
    color:#fff;  
    border:none;  
    padding:0 24px;  
    border-radius:50px;  
    font-weight:600;  
    font-family: 'Poppins', sans-serif;  
    cursor:pointer;  
    transition:0.2s;  
    display: flex;  
    align-items: center;  
    gap: 8px;  
    font-size: 15px;  
}  
.search-box button i {  
    font-size: 16px;  
}  
.search-box button:hover{  
    background: linear-gradient(135deg, #0b3b6b, #072c4a);  
    transform:scale(1.02);  
}  

/* If you want to use an image instead of icon, uncomment below and add search.png */  
/* .search-box button i { display: none; }  
.search-box button { background-image: url('search.png'); background-size: 24px; background-repeat: no-repeat; background-position: center; text-indent: -9999px; width: 54px; padding: 0; } */  

/* ================= REPORTS GRID ================= */  
.reports{  
    display:grid;  
    grid-template-columns:repeat(auto-fit,minmax(300px,1fr));  
    gap:20px;  
}  
.card{  
    background:#fff;  
    border-radius:24px;  
    overflow:hidden;  
    box-shadow:0 12px 24px rgba(0,0,0,0.08);  
    transition:0.25s ease;  
    cursor: pointer;  
    border:1px solid #e2e8f0;  
}  
.card:hover{  
    transform:translateY(-6px);  
    box-shadow:0 20px 30px rgba(0,0,0,0.12);  
    border-color:#1e4a7a;  
}  

.card-header{  
    background: linear-gradient(135deg, #1e4a7a, #0b3b6b);  
    color:#fff;  
    padding:18px;  
}  
.card-header h3{  
    font-size:1.2rem;  
    font-weight:700;  
    pointer-events: none;  
}  

.badge{  
    background:#2c6e9e;  
    color:#fff;  
    display:inline-block;  
    padding:4px 14px;  
    border-radius:40px;  
    font-size:11px;  
    font-weight:700;  
    margin-top:8px;  
}  

.card-body{padding:18px}  
.label{  
    font-size:12px;  
    font-weight:700;  
    margin-top:12px;  
    color:#1e4a7a;  
    text-transform:uppercase;  
    letter-spacing:0.5px;  
}  
.value{ font-size:14px; color:#2d3e50; font-weight:500; }  

.card-body a {  
    pointer-events: auto;  
    position: relative;  
    z-index: 2;  
    color:#1e4a7a;  
    text-decoration:none;  
    font-weight:600;  
}  
.card-body a:hover { color:#0b3b6b; text-decoration:underline; }  

.card-footer{  
    background:#f8fafc;  
    padding:12px 18px;  
    display:flex;  
    justify-content:space-between;  
    font-size:12px;  
    color:#64748b;  
    border-top:1px solid #e2e8f0;  
}  

.images{ display:flex; gap:8px; margin-top:12px; flex-wrap:wrap; }  
.images img{  
    width:70px;  
    height:70px;  
    border-radius:16px;  
    object-fit:cover;  
    border:2px solid #cbd5e1;  
}  

.floating{  
    position:fixed;  
    bottom:20px;  
    right:20px;  
    background: linear-gradient(135deg, #1e4a7a, #0b3b6b);  
    color:#fff;  
    padding:14px 22px;  
    border-radius:60px;  
    text-decoration:none;  
    font-weight:800;  
    font-family: 'Poppins', sans-serif;  
    box-shadow:0 8px 20px rgba(0,0,0,0.2);  
    z-index:1000;  
    transition:0.2s;  
}  
.floating:hover{ background: linear-gradient(135deg, #0b3b6b, #072c4a); transform:scale(1.05); }  

/* ================= FOOTER - GREY BACKGROUND ================= */  
.footer{  
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

/* ================= MOBILE EXTRA FIXES ================= */  
@media(max-width:768px){  
    .slider-header { flex-direction: column; gap: 12px; }  
    .slide-card { flex: 0 0 250px; }  
    .stats { margin-top: 20px; gap: 12px; }  
    .stat h2 { font-size: 22px; }  
    .search-box {  
        top: 5px;  
        padding: 6px;  
        gap: 6px;  
    }  
    .search-box input { padding: 12px 16px; font-size: 14px; }  
    .search-box button { padding: 0 18px; font-size: 13px; }  
    .search-box button i { font-size: 14px; }  
    .footer a { margin: 0 8px; }  
    .card-body { padding: 14px; }  
    .card-header h3 { font-size: 1rem; }  
}  

/* For very small devices */  
@media(max-width:480px){  
    .slide-card { flex: 0 0 220px; }  
    .stat { padding: 14px 8px; }  
    .stat h2 { font-size: 20px; }  
    .search-box button span { display: none; }  
    .search-box button i { margin: 0; font-size: 18px; }  
    .search-box button { padding: 0 16px; }  
}  
</style>  
</head>  
<body>  

<!-- HEADER - STRIKING BLUE -->  
<div class="header">  
    <div class="logo">  
        <img src="MART72.JPG" alt="GSM Mart 72 Logo">  
    </div>  
    <p>MART72 CREATED A SCAMMER LIST __ VISIT, SEARCH , AND VERIFY YOURSELF</p>  
    <div class="nav-links">  
        <a href="reports.php">➕ Report</a>  
        <a href="#list">📋 Reports</a>  
        <a href="about.php">🔒 ABOUT US</a>  
    </div>  
</div>  

<!-- RECENT REPORTS SLIDER -->  
<?php if ($has_recent): ?>  
<div class="slider-section">  
    <div class="slider-header">  
        <h2><i class="fas fa-history"></i> Recent Scammer Reports</h2>  
        <div class="slider-controls">  
            <button class="slider-btn" id="prevSlideBtn"><i class="fas fa-chevron-left"></i></button>  
            <button class="slider-btn" id="nextSlideBtn"><i class="fas fa-chevron-right"></i></button>  
        </div>  
    </div>  
    <div class="slider-wrapper" id="sliderWrapper">  
        <div class="slider-track" id="sliderTrack">  
            <?php while($slide = $recent_result->fetch_assoc()):   
                $first_image = !empty($slide['image1']) ? htmlspecialchars($slide['image1']) : '';  
                $desc_short = mb_substr(htmlspecialchars($slide['short_description']), 0, 70);  
            ?>  
            <a href="single.php?id=<?= $slide['id'] ?>" class="slide-card">  
                <div class="slide-img">  
                    <?php if($first_image): ?>  
                        <img src="<?= $first_image ?>" alt="evidence" loading="lazy">  
                    <?php else: ?>  
                        <div class="no-img"><i class="fas fa-shield-alt"></i></div>  
                    <?php endif; ?>  
                </div>  
                <div class="slide-info">  
                    <h4><?= htmlspecialchars($slide['scammer_name']) ?></h4>  
                    <div class="slide-mobile"><i class="fas fa-phone-alt"></i> <?= htmlspecialchars($slide['scammer_mobile']) ?></div>  
                    <div class="slide-desc"><?= $desc_short ?>...</div>  
                    <div class="slide-date"><i class="far fa-calendar-alt"></i> <?= date('d M Y', strtotime($slide['approved_date'])) ?></div>  
                </div>  
            </a>  
            <?php endwhile; ?>  
        </div>  
    </div>  
</div>  
<?php endif; ?>  

<div class="container">  
    <!-- STATS -->  
    <div class="stats">  
        <div class="stat"><h2><i class="fas fa-check-circle"></i> <?= $stats['total'] ?></h2><p>Verified Reports</p></div>  
        <div class="stat"><h2><i class="fas fa-user-secret"></i> <?= $stats['unique_scammers'] ?></h2><p>Unique Scammers</p></div>  
        <div class="stat"><h2><i class="fas fa-clock"></i> <?= $stats['last_verified'] ? date('M d',strtotime($stats['last_verified'])):'N/A' ?></h2><p>Last Verified</p></div>  
    </div>  

    <!-- BEAUTIFUL SEARCH BAR (mobile friendly) -->  
    <form class="search-box" method="get" action="">  
        <input type="text" name="search" placeholder="🔍 Search by name, mobile or description..."  
               value="<?= htmlspecialchars($search) ?>">  
        <button type="submit"><i class="fas fa-search"></i> <span>Search</span></button>  
    </form>  

    <!-- REPORTS GRID (clickable cards) -->  
    <div class="reports" id="list">  
        <?php if($result->num_rows): while($r=$result->fetch_assoc()): ?>  
        <div class="card" data-id="<?= $r['id'] ?>">  
            <div class="card-header">  
                <h3><?= htmlspecialchars($r['scammer_name']) ?></h3>  
                <span class="badge">✅ Verified</span>  
            </div>  
            <div class="card-body">  
                <div class="label"><i class="fas fa-mobile-alt"></i> Mobile</div>  
                <div class="value">  
                    <a href="tel:<?= $r['scammer_mobile'] ?>">📞 <?= $r['scammer_mobile'] ?></a>  
                </div>  
                <?php if($r['scammer_whatsapp']): ?>  
                <div class="label"><i class="fab fa-whatsapp"></i> WhatsApp</div>  
                <div class="value">  
                    <a href="https://wa.me/<?= $r['scammer_whatsapp'] ?>" target="_blank">💬 <?= $r['scammer_whatsapp'] ?></a>  
                </div>  
                <?php endif; ?>  
                <div class="label"><i class="fas fa-align-left"></i> Description</div>  
                <div class="value"><?= htmlspecialchars($r['short_description']) ?></div>  
                <?php if($r['image1'] || $r['image2']): ?>  
                <div class="images">  
                    <?php if($r['image1']): ?><img src="<?= $r['image1'] ?>" alt="proof"><?php endif; ?>  
                    <?php if($r['image2']): ?><img src="<?= $r['image2'] ?>" alt="proof"><?php endif; ?>  
                </div>  
                <?php endif; ?>  
            </div>  
            <div class="card-footer">  
                <span>#<?= $r['id'] ?></span>  
                <span><i class="far fa-calendar"></i> <?= date('M d, Y',strtotime($r['approved_date'])) ?></span>  
            </div>  
        </div>  
        <?php endwhile; else: ?>  
        <p style="text-align:center;padding:40px; background:#fff; border-radius:30px;">No verified reports found</p>  
        <?php endif; ?>  
    </div>  
</div>  

<!-- FLOATING REPORT BUTTON -->  
<a href="reports.php" class="floating">➕ Report Scammer</a>  

<!-- FOOTER WITH GREY BACKGROUND AND LINKS -->  
<div class="footer">  
    <div>  
        <a href="#list">📋 Reports</a>  
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

<script>  
    // Make main cards clickable (opens single.php)  
    document.querySelectorAll('.card').forEach(card => {  
        const reportId = card.getAttribute('data-id');  
        if (!reportId) return;  
        card.addEventListener('click', function(e) {  
            let target = e.target;  
            while (target && target !== card) {  
                if (target.tagName === 'A' && target.getAttribute('href')) {  
                    return;  
                }  
                target = target.parentElement;  
            }  
            window.location.href = `single.php?id=${reportId}`;  
        });  
    });  

    // Slider auto-scroll with blue buttons  
    const wrapper = document.getElementById('sliderWrapper');  
    const track = document.getElementById('sliderTrack');  
    const prevBtn = document.getElementById('prevSlideBtn');  
    const nextBtn = document.getElementById('nextSlideBtn');  
    let autoInterval;  
    let isHovering = false;  

    function scrollByStep(direction) {  
        if(!wrapper) return;  
        const slide = document.querySelector('.slide-card');  
        if(!slide) return;  
        const slideWidth = slide.offsetWidth;  
        const gap = 20;  
        const scrollAmount = (slideWidth + gap) * direction;  
        wrapper.scrollBy({ left: scrollAmount, behavior: 'smooth' });  
    }  

    function startAutoSlide() {  
        if(autoInterval) clearInterval(autoInterval);  
        autoInterval = setInterval(() => {  
            if(!isHovering && wrapper) {  
                const maxScroll = wrapper.scrollWidth - wrapper.clientWidth;  
                if(wrapper.scrollLeft + wrapper.clientWidth >= maxScroll - 10) {  
                    wrapper.scrollTo({ left: 0, behavior: 'smooth' });  
                } else {  
                    scrollByStep(1);  
                }  
            }  
        }, 5000);  
    }  

    if(wrapper && prevBtn && nextBtn && track && track.children.length > 2) {  
        prevBtn.addEventListener('click', () => { scrollByStep(-1); resetAutoTimer(); });  
        nextBtn.addEventListener('click', () => { scrollByStep(1); resetAutoTimer(); });  
        wrapper.addEventListener('mouseenter', () => { isHovering = true; });  
        wrapper.addEventListener('mouseleave', () => { isHovering = false; });  
        startAutoSlide();  
    }  

    function resetAutoTimer() {  
        if(autoInterval) { clearInterval(autoInterval); startAutoSlide(); }  
    }  

    if(wrapper && track && track.children.length <= 2) {  
        if(prevBtn) prevBtn.style.display = 'none';  
        if(nextBtn) nextBtn.style.display = 'none';  
    }  
</script>  

</body>  
</html>  
<?php $conn->close(); ?>  