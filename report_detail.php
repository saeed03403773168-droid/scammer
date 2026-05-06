<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors',1);

/* ===== DATABASE ===== */
$conn = new mysqli("localhost","marttop_scammer","saeed@saif1122","marttop_scammer");
if($conn->connect_error){ die("DB Error"); }

/* ===== GET ID ===== */
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if($id <= 0){ die("Invalid ID"); }

/* ===== FETCH REPORT ===== */
$q = $conn->query("SELECT * FROM reports WHERE id=$id");
if($q->num_rows == 0){ die("Report not found"); }
$r = $q->fetch_assoc();

/* ===== FETCH IMAGES ===== */
$images = [];
$iq = $conn->query("SELECT image FROM report_images WHERE report_id=$id");
while($row = $iq->fetch_assoc()){
    $images[] = $row['image'];
}

$status = $r['status'] ?? 'pending'; // verified | pending
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Report Detail</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
:root{
    --bg:#f4f6fb;
    --card:#fff;
    --text:#333;
}
.dark{
    --bg:#0f1220;
    --card:#171a2b;
    --text:#eee;
}
body{
    margin:0;
    font-family:Segoe UI,sans-serif;
    background:var(--bg);
    color:var(--text);
    transition:.3s;
}
.container{
    max-width:1100px;
    margin:40px auto;
    padding:20px;
}
.card{
    background:var(--card);
    border-radius:20px;
    box-shadow:0 25px 60px rgba(0,0,0,.2);
    padding:30px;
}
.header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    flex-wrap:wrap;
}
h1{
    margin:0;
    font-size:28px;
}
.badge{
    padding:8px 18px;
    border-radius:30px;
    font-weight:600;
    font-size:14px;
}
.verified{background:#e7f9ef;color:#1e8449;}
.pending{background:#fff3cd;color:#856404;}

.toggle{
    cursor:pointer;
    font-size:14px;
    padding:8px 14px;
    border-radius:20px;
    background:#eee;
}
.grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:25px;
    margin-top:30px;
}
.box{
    background:rgba(0,0,0,.03);
    padding:20px;
    border-radius:16px;
}
.box h3{margin-top:0;}
.box p{margin:6px 0;font-size:15px;}

.gallery{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(180px,1fr));
    gap:15px;
    margin-top:30px;
}
.gallery img{
    width:100%;
    height:160px;
    object-fit:cover;
    border-radius:14px;
    cursor:pointer;
    transition:.3s;
}
.gallery img:hover{transform:scale(1.05);}

.footer{
    margin-top:30px;
    display:flex;
    justify-content:space-between;
    flex-wrap:wrap;
    align-items:center;
}
.back{
    text-decoration:none;
    background:linear-gradient(135deg,#667eea,#764ba2);
    color:#fff;
    padding:12px 26px;
    border-radius:40px;
    font-weight:600;
}

/* IMAGE MODAL */
.modal{
    position:fixed;
    inset:0;
    background:rgba(0,0,0,.85);
    display:none;
    align-items:center;
    justify-content:center;
    z-index:999;
}
.modal img{
    max-width:90%;
    max-height:90%;
    border-radius:16px;
}
.modal span{
    position:absolute;
    top:20px;
    right:30px;
    font-size:40px;
    color:#fff;
    cursor:pointer;
}

@media(max-width:768px){
    .grid{grid-template-columns:1fr;}
}
</style>
</head>

<body>
<div class="container">
<div class="card">

    <div class="header">
        <h1><?php echo htmlspecialchars($r['name']); ?></h1>
        <div>
            <span class="badge <?php echo $status=='verified'?'verified':'pending'; ?>">
                <?php echo ucfirst($status); ?>
            </span>
            <span class="toggle" onclick="toggleDark()">🌙</span>
        </div>
    </div>

    <div class="grid">
        <div class="box">
            <h3>Scammer Information</h3>
            <p><b>Mobile:</b> <?php echo htmlspecialchars($r['mobile']); ?></p>
            <p><b>WhatsApp:</b> <?php echo htmlspecialchars($r['whatsapp']); ?></p>
            <p><b>Description:</b><br><?php echo nl2br(htmlspecialchars($r['description'])); ?></p>
        </div>

        <div class="box">
            <h3>Reporter Information</h3>
            <p><b>Name:</b> <?php echo htmlspecialchars($r['reporter_name']); ?></p>
            <p><b>Email:</b> <?php echo htmlspecialchars($r['reporter_email']); ?></p>
            <p><b>Date:</b> <?php echo date("d M Y",strtotime($r['created_at'])); ?></p>
        </div>
    </div>

    <?php if($images){ ?>
    <h3 style="margin-top:35px;">Evidence Images</h3>
    <div class="gallery">
        <?php foreach($images as $img){ ?>
            <img src="uploads/<?php echo htmlspecialchars($img); ?>" onclick="openImg(this.src)">
        <?php } ?>
    </div>
    <?php } ?>

    <div class="footer">
        <div>Report ID: #<?php echo $id; ?></div>
        <a href="reports.php" class="back">← Back</a>
    </div>

</div>
</div>

<div class="modal" id="modal">
    <span onclick="closeImg()">&times;</span>
    <img id="modalImg">
</div>

<script>
function openImg(src){
    document.getElementById('modal').style.display='flex';
    document.getElementById('modalImg').src=src;
}
function closeImg(){
    document.getElementById('modal').style.display='none';
}
function toggleDark(){
    document.body.classList.toggle('dark');
}
</script>

</body>
</html>
