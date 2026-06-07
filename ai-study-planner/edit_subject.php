<?php
include("db.php");

$id = $_GET['id'];

$result = mysqli_query($conn,
"SELECT * FROM subjects WHERE id='$id'");

$row = mysqli_fetch_assoc($result);

if(isset($_POST['update']))
{
    $subject = $_POST['subject'];
    $teacher = $_POST['teacher'];
    $units = $_POST['units'];

    mysqli_query($conn,
    "UPDATE subjects
    SET subject_name='$subject',
        teacher_name='$teacher',
        total_units='$units'
    WHERE id='$id'");

    header("Location: subjects.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Subject</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">

<h2>Edit Subject</h2>

<form method="POST">

<input type="text" name="subject"
value="<?php echo $row['subject_name']; ?>"
class="form-control mb-3">

<input type="text" name="teacher"
value="<?php echo $row['teacher_name']; ?>"
class="form-control mb-3">

<input type="number" name="units"
value="<?php echo $row['total_units']; ?>"
class="form-control mb-3">

<button type="submit"
name="update"
class="btn btn-success">
Update Subject
</button>

</form>

</div>

</body>
</html>