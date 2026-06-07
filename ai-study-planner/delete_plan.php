<?php
include("db.php");

$id = $_GET['id'];

mysqli_query($conn,
"DELETE FROM study_plans WHERE id='$id'");

header("Location: planner.php");
exit();
?>