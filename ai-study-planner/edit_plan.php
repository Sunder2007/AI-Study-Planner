<?php
include("db.php");

$id = $_GET['id'];

$result = mysqli_query($conn,
"SELECT * FROM study_plans WHERE id='$id'");

$row = mysqli_fetch_assoc($result);

if(isset($_POST['update']))
{
    $subject = $_POST['subject_name'];
    $date = $_POST['study_date'];
    $hours = $_POST['study_hours'];

    mysqli_query($conn,
    "UPDATE study_plans
    SET subject_name='$subject',
        study_date='$date',
        study_hours='$hours'
    WHERE id='$id'");

    header("Location: planner.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Plan</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">

<h2>Edit Study Plan</h2>

<form method="POST">

<input type="text"
name="subject_name"
value="<?php echo $row['subject_name']; ?>"
class="form-control mb-3">

<input type="date"
name="study_date"
value="<?php echo $row['study_date']; ?>"
class="form-control mb-3">

<input type="number"
name="study_hours"
value="<?php echo $row['study_hours']; ?>"
class="form-control mb-3">

<button type="submit"
name="update"
class="btn btn-success">
Update Plan
</button>

</form>

</div>

</body>
</html>