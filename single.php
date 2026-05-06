<?php
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

$conn = new mysqli("localhost","marttop_scammer","saeed@saif1122","marttop_scammer");
if($conn->connect_error) die("DB Error");
$id = intval($_GET['id'] ?? 0);
$q = $conn->prepare("SELECT * FROM reports WHERE id=? AND status='approved'");
$q->bind_param("i",$id);
$q->execute();
$r = $q->get_result()->fetch_assoc();
if(!$r) die("Report not found");

// Clean image paths - remove 'uploads/' prefix if it exists in database
$image1 = !empty($r['image1']) ? str_replace('uploads/', '', trim($r['image1'])) : '';
$image2 = !empty($r['image2']) ? str_replace('uploads/', '', trim($r['image2'])) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($r['scammer_name']) ?> - Verified Scammer | MART72</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes, viewport-fit=cover">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Inter', sans-serif;
    background: #f0f4f8;
    min-height: 100vh;
}

h1, h2, h3, .header h1, .back-btn {
    font-family: 'Poppins', sans-serif;
}

/* ================= HEADER - BLUE THEME ================= */
.header {
    background: linear-gradient(135deg, #0b3b6b, #1e4a7a);
    padding: 25px 20px;
    color: #fff;
    text-align: center;
    box-shadow: 0 6px 20px rgba(0,0,0,0.2);
}

.header img {
    width: 90px;
    background: #fff;
    padding: 8px;
    border-radius: 16px;
    border: 2px solid #6ea8fe;
    box-shadow: 0 6px 15px rgba(0,0,0,0.2);
    margin-bottom: 12px;
    transition: 0.3s;
}

.header img:hover {
    transform: scale(1.05);
}

.header h1 {
    font-size: 1.8rem;
    margin: 0;
    font-weight: 700;
    letter-spacing: -0.5px;
}

/* ================= CONTAINER ================= */
.container {
    max-width: 1000px;
    margin: 30px auto;
    padding: 0 20px;
}

/* ================= MAIN CARD ================= */
.box {
    background: #fff;
    border-radius: 32px;
    overflow: hidden;
    box-shadow: 0 20px 35px rgba(0,0,0,0.1);
    border: 1px solid #e2e8f0;
    animation: slideUp 0.5s ease;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Section styling */
.section-title {
    color: #1e4a7a;
    font-size: 1.3rem;
    font-weight: 700;
    margin: 25px 0 15px 0;
    padding: 10px 20px;
    background: #eef2ff;
    border-left: 5px solid #1e4a7a;
    display: flex;
    align-items: center;
    gap: 12px;
}

.page-title {
    color: #1e2a3a;
    font-size: 2rem;
    font-weight: 800;
    margin: 0;
    padding: 25px 25px 0 25px;
    text-align: center;
    word-break: break-word;
}

/* Info rows */
.info-row {
    padding: 12px 25px;
    border-bottom: 1px solid #eef2f6;
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    align-items: flex-start;
}

.info-row:last-child {
    border-bottom: none;
}

.label {
    font-weight: 700;
    color: #1e4a7a;
    min-width: 180px;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.value {
    color: #2d3e50;
    flex: 1;
    font-size: 1rem;
    font-weight: 500;
    word-break: break-word;
}

.description-box {
    background: #f8fafc;
    padding: 16px 20px;
    border-radius: 20px;
    border-left: 4px solid #1e4a7a;
    margin: 0 25px 15px 25px;
    line-height: 1.6;
    color: #334155;
}

/* Proof section */
.proof-section {
    margin: 20px 25px 25px 25px;
    padding: 20px;
    background: #f8fafc;
    border-radius: 24px;
}

.proof-section h3 {
    color: #1e2a3a;
    font-size: 1.2rem;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.screenshot-count {
    color: #1e4a7a;
    font-size: 0.85rem;
    font-weight: 600;
    margin-bottom: 15px;
}

.images {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 20px;
    margin-top: 10px;
}

.image-wrapper {
    position: relative;
    border-radius: 20px;
    overflow: hidden;
    cursor: pointer;
    background: #fff;
    border: 2px solid #e2e8f0;
    transition: all 0.25s ease;
}

.image-wrapper:hover {
    transform: translateY(-5px);
    border-color: #1e4a7a;
    box-shadow: 0 12px 25px rgba(30,74,122,0.2);
}

.image-wrapper img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    display: block;
}

.image-label {
    position: absolute;
    top: 12px;
    right: 12px;
    background: rgba(0,0,0,0.7);
    color: #fff;
    padding: 4px 12px;
    border-radius: 30px;
    font-size: 12px;
    font-weight: 600;
}

.zoom-icon {
    position: absolute;
    bottom: 12px;
    right: 12px;
    background: #1e4a7a;
    color: #fff;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.2s;
}

.image-wrapper:hover .zoom-icon {
    opacity: 1;
}

/* Verified badge */
.verified-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #dcfce7;
    color: #15803d;
    padding: 6px 16px;
    border-radius: 40px;
    font-weight: 700;
    font-size: 0.85rem;
}

/* Back button */
.back-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    margin: 20px 25px 30px 25px;
    padding: 14px 28px;
    background: linear-gradient(135deg, #1e4a7a, #0b3b6b);
    color: #fff;
    border-radius: 60px;
    text-decoration: none;
    font-weight: 700;
    transition: all 0.25s ease;
    box-shadow: 0 4px 12px rgba(30,74,122,0.3);
}

.back-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(30,74,122,0.4);
}

/* Lightbox (unchanged functionality, restyled) */
.lightbox {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.95);
    z-index: 9999;
    justify-content: center;
    align-items: center;
    padding: 20px;
}

.lightbox.active {
    display: flex;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.lightbox-content {
    position: relative;
    max-width: 90%;
    max-height: 90%;
    animation: zoomIn 0.3s ease;
}

@keyframes zoomIn {
    from {
        opacity: 0;
        transform: scale(0.9);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.lightbox img {
    max-width: 100%;
    max-height: 85vh;
    border-radius: 16px;
    box-shadow: 0 0 30px rgba(255,255,255,0.2);
}

.lightbox-close {
    position: absolute;
    top: -50px;
    right: 0;
    color: #fff;
    font-size: 36px;
    cursor: pointer;
    background: rgba(255,255,255,0.1);
    width: 45px;
    height: 45px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: 0.2s;
}

.lightbox-close:hover {
    background: #ff4757;
    transform: rotate(90deg);
}

.lightbox-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(255,255,255,0.2);
    color: #fff;
    font-size: 28px;
    padding: 18px;
    cursor: pointer;
    border-radius: 50%;
    transition: 0.2s;
    width: 55px;
    height: 55px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.lightbox-nav:hover {
    background: rgba(255,255,255,0.3);
    transform: translateY(-50%) scale(1.1);
}

.lightbox-prev {
    left: 20px;
}

.lightbox-next {
    right: 20px;
}

.lightbox-counter {
    position: absolute;
    bottom: -50px;
    left: 50%;
    transform: translateX(-50%);
    color: #fff;
    font-size: 16px;
    background: rgba(255,255,255,0.15);
    padding: 8px 20px;
    border-radius: 40px;
    font-weight: 600;
}

/* ================= MOBILE RESPONSIVE ================= */
@media (max-width: 768px) {
    .container {
        margin: 20px auto;
        padding: 0 16px;
    }
    
    .header h1 {
        font-size: 1.4rem;
    }
    
    .page-title {
        font-size: 1.5rem;
        padding: 20px 20px 0 20px;
    }
    
    .section-title {
        font-size: 1.1rem;
        padding: 8px 16px;
        margin: 20px 0 10px 0;
    }
    
    .info-row {
        flex-direction: column;
        gap: 6px;
        padding: 12px 20px;
    }
    
    .label {
        min-width: auto;
        font-size: 0.8rem;
    }
    
    .value {
        font-size: 0.95rem;
    }
    
    .description-box {
        margin: 0 20px 12px 20px;
        padding: 14px;
    }
    
    .proof-section {
        margin: 15px 20px 20px 20px;
        padding: 16px;
    }
    
    .images {
        grid-template-columns: 1fr;
        gap: 16px;
    }
    
    .image-wrapper img {
        height: 220px;
    }
    
    .back-btn {
        width: calc(100% - 40px);
        margin: 20px 20px 25px 20px;
        justify-content: center;
    }
    
    .lightbox-close {
        top: 10px;
        right: 10px;
        font-size: 28px;
        width: 40px;
        height: 40px;
    }
    
    .lightbox-nav {
        padding: 12px;
        font-size: 22px;
        width: 45px;
        height: 45px;
    }
    
    .lightbox-prev {
        left: 10px;
    }
    
    .lightbox-next {
        right: 10px;
    }
    
    .lightbox-counter {
        bottom: -40px;
        font-size: 14px;
        padding: 6px 16px;
    }
}

@media (max-width: 480px) {
    .container {
        margin: 15px auto;
    }
    
    .page-title {
        font-size: 1.3rem;
    }
    
    .section-title {
        font-size: 1rem;
    }
    
    .image-wrapper img {
        height: 180px;
    }
}
</style>
</head>
<body>

<div class="header">
    <img src="MART72.JPG" alt="MART72 Logo">
    <h1>Scammer Verified Profile</h1>
</div>

<div class="container">
    <div class="box">
        <h2 class="page-title"><?= htmlspecialchars($r['scammer_name']) ?></h2>
        
        <!-- Reporter Information Section -->
        <div class="section-title">
            <i class="fas fa-user-shield"></i> Reporter Information
        </div>
        
        <div class="info-row">
            <div class="label"><i class="fas fa-user"></i> Reporter:</div>
            <div class="value"><?= htmlspecialchars($r['user_name']) ?></div>
        </div>
        
        <div class="info-row">
            <div class="label"><i class="fas fa-mobile-alt"></i> Reporter Mobile:</div>
            <div class="value"><?= htmlspecialchars($r['user_mobile']) ?></div>
        </div>
        
        <?php if(!empty($r['user_email'])): ?>
        <div class="info-row">
            <div class="label"><i class="fas fa-envelope"></i> Reporter Email:</div>
            <div class="value"><?= htmlspecialchars($r['user_email']) ?></div>
        </div>
        <?php endif; ?>
        
        <!-- Scammer Information Section -->
        <div class="section-title">
            <i class="fas fa-user-times"></i> Scammer Information
        </div>
        
        <div class="info-row">
            <div class="label"><i class="fas fa-user-secret"></i> Scammer Name:</div>
            <div class="value"><?= htmlspecialchars($r['scammer_name']) ?></div>
        </div>
        
        <div class="info-row">
            <div class="label"><i class="fas fa-phone"></i> Scammer Mobile:</div>
            <div class="value"><?= htmlspecialchars($r['scammer_mobile']) ?></div>
        </div>
        
        <?php if(!empty($r['scammer_whatsapp'])): ?>
        <div class="info-row">
            <div class="label"><i class="fab fa-whatsapp"></i> Scammer WhatsApp:</div>
            <div class="value"><?= htmlspecialchars($r['scammer_whatsapp']) ?></div>
        </div>
        <?php endif; ?>
        
        <!-- Scam Details Section -->
        <div class="section-title">
            <i class="fas fa-exclamation-triangle"></i> Scam Details
        </div>
        
        <?php if(!empty($r['short_description'])): ?>
        <div class="description-box">
            <strong><i class="fas fa-align-left"></i> Description:</strong><br>
            <?= nl2br(htmlspecialchars($r['short_description'])) ?>
        </div>
        <?php endif; ?>
        
        <?php if(!empty($r['long_description'])): ?>
        <div class="description-box">
            <strong><i class="fas fa-file-alt"></i> Detailed Description:</strong><br>
            <?= nl2br(htmlspecialchars($r['long_description'])) ?>
        </div>
        <?php endif; ?>
        
        <!-- Attached Screenshots -->
        <?php if(!empty($image1) || !empty($image2)): ?>
        <div class="proof-section">
            <h3><i class="fas fa-images"></i> Attached Screenshots</h3>
            <p class="screenshot-count">
                <i class="fas fa-check-circle"></i>
                <?= (!empty($image1) ? 1 : 0) + (!empty($image2) ? 1 : 0) ?> screenshot(s) found
            </p>
            <div class="images">
                <?php if(!empty($image1)): ?>
                <div class="image-wrapper" onclick="openLightbox(0)">
                    <span class="image-label">Proof 1</span>
                    <img src="uploads/<?= htmlspecialchars($image1) ?>" alt="Proof Image 1">
                    <div class="zoom-icon"><i class="fas fa-search-plus"></i></div>
                </div>
                <?php endif; ?>
                <?php if(!empty($image2)): ?>
                <div class="image-wrapper" onclick="openLightbox(<?= !empty($image1) ? 1 : 0 ?>)">
                    <span class="image-label">Proof 2</span>
                    <img src="uploads/<?= htmlspecialchars($image2) ?>" alt="Proof Image 2">
                    <div class="zoom-icon"><i class="fas fa-search-plus"></i></div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Verification Information -->
        <div class="section-title">
            <i class="fas fa-info-circle"></i> Verification Info
        </div>
        
        <div class="info-row">
            <div class="label"><i class="fas fa-check-circle"></i> Status:</div>
            <div class="value"><span class="verified-badge"><i class="fas fa-check-circle"></i> Approved</span></div>
        </div>
        
        <div class="info-row">
            <div class="label"><i class="fas fa-calendar-check"></i> Report Date:</div>
            <div class="value"><?= date("d M Y, h:i A", strtotime($r['report_date'])) ?></div>
        </div>
        
        <?php if(!empty($r['approved_date'])): ?>
        <div class="info-row">
            <div class="label"><i class="fas fa-calendar-check"></i> Approved Date:</div>
            <div class="value"><?= date("d M Y, h:i A", strtotime($r['approved_date'])) ?></div>
        </div>
        <?php endif; ?>
        
        <a href="index.php" class="back-btn">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>
</div>

<!-- Lightbox (same functionality, updated styling) -->
<div class="lightbox" id="lightbox" onclick="closeLightbox(event)">
    <div class="lightbox-content" onclick="event.stopPropagation()">
        <span class="lightbox-close" onclick="closeLightbox(event)">&times;</span>
        <?php 
        $imageCount = (!empty($image1) ? 1 : 0) + (!empty($image2) ? 1 : 0);
        if($imageCount > 1): 
        ?>
        <div class="lightbox-nav lightbox-prev" onclick="event.stopPropagation(); changeImage(-1);">
            <i class="fas fa-chevron-left"></i>
        </div>
        <div class="lightbox-nav lightbox-next" onclick="event.stopPropagation(); changeImage(1);">
            <i class="fas fa-chevron-right"></i>
        </div>
        <?php endif; ?>
        <img id="lightbox-img" src="" alt="Proof Screenshot">
        <?php if($imageCount > 1): ?>
        <div class="lightbox-counter">
            <span id="current-index">1</span> / <?= $imageCount ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
const screenshots = [
    <?php if(!empty($image1)): ?>'uploads/<?= htmlspecialchars($image1) ?>',<?php endif; ?>
    <?php if(!empty($image2)): ?>'uploads/<?= htmlspecialchars($image2) ?>'<?php endif; ?>
].filter(Boolean);

let currentIndex = 0;

function openLightbox(index) {
    currentIndex = index;
    updateLightboxImage();
    document.getElementById('lightbox').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeLightbox(event) {
    if (event) event.stopPropagation();
    document.getElementById('lightbox').classList.remove('active');
    document.body.style.overflow = 'auto';
}

function changeImage(direction) {
    currentIndex += direction;
    if (currentIndex < 0) currentIndex = screenshots.length - 1;
    if (currentIndex >= screenshots.length) currentIndex = 0;
    updateLightboxImage();
}

function updateLightboxImage() {
    document.getElementById('lightbox-img').src = screenshots[currentIndex];
    const counter = document.getElementById('current-index');
    if (counter) {
        counter.textContent = currentIndex + 1;
    }
}

// Keyboard navigation
document.addEventListener('keydown', function(e) {
    const lightbox = document.getElementById('lightbox');
    if (lightbox.classList.contains('active')) {
        if (e.key === 'Escape') {
            closeLightbox();
        } else if (e.key === 'ArrowLeft') {
            changeImage(-1);
        } else if (e.key === 'ArrowRight') {
            changeImage(1);
        }
    }
});

// Touch swipe for mobile
let touchStartX = 0;
let touchEndX = 0;
const lightboxElement = document.getElementById('lightbox');
lightboxElement.addEventListener('touchstart', function(e) {
    touchStartX = e.changedTouches[0].screenX;
}, { passive: true });
lightboxElement.addEventListener('touchend', function(e) {
    touchEndX = e.changedTouches[0].screenX;
    const swipeThreshold = 50;
    if (touchEndX < touchStartX - swipeThreshold) changeImage(1);
    if (touchEndX > touchStartX + swipeThreshold) changeImage(-1);
}, { passive: true });
</script>
</body>
</html>