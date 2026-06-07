<?php

include("db.php");

$id = $_GET['id'];

mysqli_query($conn,
"DELETE FROM progress WHERE id='$id'");

header("Location: progress.php");

?>