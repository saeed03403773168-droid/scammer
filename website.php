<?php
// Redirect after 3 seconds
header("refresh:3;url=https://gsmmart72.com");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Redirecting...</title>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

<style>

body{
margin:0;
height:100vh;
display:flex;
align-items:center;
justify-content:center;
background:linear-gradient(135deg,#0f2027,#203a43,#2c5364);
font-family:'Poppins',sans-serif;
color:white;
}

.box{
background:rgba(255,255,255,0.1);
padding:40px;
border-radius:15px;
text-align:center;
box-shadow:0 10px 30px rgba(0,0,0,0.4);
max-width:350px;
width:90%;
}

h1{
font-size:22px;
margin-bottom:10px;
}

p{
font-size:14px;
opacity:0.9;
}

.loader{
width:50px;
height:50px;
border:5px solid rgba(255,255,255,0.3);
border-top:5px solid #25D366;
border-radius:50%;
margin:20px auto;
animation:spin 1s linear infinite;
}

@keyframes spin{
0%{transform:rotate(0deg);}
100%{transform:rotate(360deg);}
}

a{
color:#25D366;
text-decoration:none;
font-size:14px;
}

</style>
</head>

<body>

<div class="box">

<h1>Redirecting...</h1>

<div class="loader"></div>

<p>
You are being redirected to<br>
<b>GSM Mart 72</b>
</p>

<p>
If not redirected,
<a href="https://gsmmart72.com">Click Here</a>
</p>

</div>

</body>
</html>