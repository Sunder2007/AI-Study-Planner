<?php
session_start();

if(!isset($_SESSION['email']))
{
    header("Location: index.php");
    exit();
}

include("db.php");

$user_id = $_SESSION['user_id'];

if(isset($_POST['save']))
{
    $subject = $_POST['subject_name'];
    $date = $_POST['exam_date'];

    mysqli_query($conn,
    "INSERT INTO exams(subject_name,exam_date,user_id)
    VALUES('$subject','$date','$user_id')");

    header("Location: exam.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Exam Timetable</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow">
<div class="container">

<a class="navbar-brand" href="dashboard.php">
🤖 AI Study Planner
</a>

<div>

<a href="dashboard.php" class="btn btn-light btn-sm me-2">
🏠 Dashboard
</a>

<a href="subjects.php" class="btn btn-light btn-sm me-2">
📚 Subjects
</a>

<a href="planner.php" class="btn btn-light btn-sm me-2">
📅 Planner
</a>

<a href="progress.php" class="btn btn-light btn-sm me-2">
📊 Progress
</a>

<a href="exam.php" class="btn btn-warning btn-sm me-2">
📝 Exams
</a>

<a href="logout.php" class="btn btn-danger btn-sm">
🚪 Logout
</a>

</div>

</div>
</nav>

<div class="container mt-4">

<h2>📅 Exam Timetable Generator</h2>

<form method="POST">

<label>Subject</label>

<select name="subject_name" class="form-control mb-3" required>

<option value="">Select Subject</option>

<?php

$result = mysqli_query($conn,
"SELECT * FROM subjects WHERE user_id='$user_id'");

while($row = mysqli_fetch_assoc($result))
{
?>

<option value="<?php echo $row['subject_name']; ?>">
<?php echo $row['subject_name']; ?>
</option>

<?php
}
?>

</select>

<label>Exam Date</label>

<input type="date"
name="exam_date"
class="form-control mb-3"
required>

<button type="submit"
name="save"
class="btn btn-success">
Save Exam
</button>

</form>

<h3 class="mt-5">Upcoming Exams</h3>

<table class="table table-bordered">

<tr>
<th>Subject</th>
<th>Exam Date</th>
<th>Days Left</th>
<th>Action</th>
</tr>

<?php

$examResult = mysqli_query($conn,
"SELECT * FROM exams
WHERE user_id='$user_id'
ORDER BY exam_date ASC");

while($exam = mysqli_fetch_assoc($examResult))
{
    $today = strtotime(date("Y-m-d"));
    $examDay = strtotime($exam['exam_date']);

    $daysLeft = ceil(($examDay - $today) / (60 * 60 * 24));
?>
<tr>

<td><?php echo $exam['subject_name']; ?></td>

<td>
<?php echo date("d M Y",
strtotime($exam['exam_date'])); ?>
</td>

<td>
<?php echo $daysLeft; ?> Days
</td>

<td>
<a href="delete_exam.php?id=<?php echo $exam['id']; ?>"
class="btn btn-danger btn-sm">
Delete
</a>
</td>

</tr>
<?php
}

?>

</table>

</div>

</body>
</html>