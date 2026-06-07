<?php

$conn = mysqli_connect("127.0.0.1","root","","study_planner");

if(!$conn)
{
    die("Connection Failed: " . mysqli_connect_error());
}

?>