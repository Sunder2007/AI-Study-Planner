<?php
session_start();
include("db.php");

$id = $_GET['id'];

$result = mysqli_query($conn,
"SELECT * FROM progress WHERE id='$id'");

$row = mysqli_fetch_assoc($result);

if(isset($_POST['update']))
{
    $completed = $_POST['completed_units'];
    $total = $_POST['total_units'];

    mysqli_query($conn,
    "UPDATE progress SET
    completed_units='$completed',
    total_units='$total'
    WHERE id='$id'");

    header("Location: progress.php");
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Progress</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">

<h2>Edit Progress</h2>

<form method="POST">

<label>Subject</label>

<input type="text"
class="form-control mb-3"
value="<?php echo $row['subject_name']; ?>"
readonly>

<label>Completed Units</label>

<input type="number"
name="completed_units"
class="form-control mb-3"
value="<?php echo $row['completed_units']; ?>"
required>

<label>Total Units</label>

<input type="number"
name="total_units"
class="form-control mb-3"
value="<?php echo $row['total_units']; ?>"
required>

<button type="submit"
name="update"
class="btn btn-success">
Update Progress
</button>

</form>

</div>

</body>
</html>