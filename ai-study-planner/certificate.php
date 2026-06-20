<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>

<title>Certificate</title>

<style>

body{
text-align:center;
font-family:Arial;
padding-top:100px;
background:#f4f4f4;
}

.certificate{
width:900px;
margin:auto;
background:white;
padding:50px;
border:10px solid #0d6efd;
}

h1{
color:#0d6efd;
}

</style>

</head>

<body>

<div class="certificate">

<h1>🏆 Certificate of Achievement</h1>

<h2><?php echo $_SESSION['name']; ?></h2>

<p>
Successfully completed study planning and exam preparation using
AI Study Planner.
</p>

<h3>Date:
<?php echo date("d M Y"); ?>
</h3>

<br><br>

<h4>AI Study Planner</h4>

</div>

</body>
</html>