<?php
include("db.php");

$id = $_GET['id'];

mysqli_query($conn,"DELETE FROM subjects WHERE id='$id'");

header("Location: subjects.php");
exit();
?>