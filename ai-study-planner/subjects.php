<?php
session_start();

if(!isset($_SESSION['email']))
{
    header("Location: index.php");
    exit();
}
include("db.php");

if(isset($_POST['add']))
{
    $subject = $_POST['subject'];
    $teacher = $_POST['teacher'];
    $units = $_POST['units'];

    $user_id = $_SESSION['user_id'];

  $sql = "INSERT INTO subjects
       (subject_name,teacher_name,total_units,user_id)
 VALUES
       ('$subject','$teacher','$units','$user_id')";

    mysqli_query($conn,$sql);
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Subjects</title>
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

<a href="subjects.php" class="btn btn-warning btn-sm me-2">
📚 Subjects
</a>

<a href="planner.php" class="btn btn-light btn-sm me-2">
📅 Planner
</a>

<a href="progress.php" class="btn btn-light btn-sm me-2">
📊 Progress
</a>

<a href="logout.php" class="btn btn-danger btn-sm">
🚪 Logout
</a>

</div>

</div>
</nav>

<div class="container mt-4">

<h2>Add Subject</h2>

<form method="POST">

<input type="text" name="subject" class="form-control mb-3" placeholder="Subject Name" required>

<input type="text" name="teacher" class="form-control mb-3" placeholder="Teacher Name" required>

<input type="number" name="units" class="form-control mb-3" placeholder="Total Units" required>

<button type="submit" name="add" class="btn btn-success">
Add Subject
</button>

</form>

<h3 class="mt-5">All Subjects</h3>

<table class="table table-bordered">
<tr>
    <th>ID</th>
    <th>Subject Name</th>
    <th>Teacher Name</th>
    <th>Total Units</th>
    <th>Action</th>
</tr>

<?php
 
$user_id = $_SESSION['user_id'];

$result = mysqli_query($conn,
"SELECT * FROM subjects WHERE user_id='$user_id'");

while($row = mysqli_fetch_assoc($result))
{
?>
<tr>
    <td><?php echo $row['id']; ?></td>
    <td><?php echo $row['subject_name']; ?></td>
    <td><?php echo $row['teacher_name']; ?></td>
    <td><?php echo $row['total_units']; ?></td>
<td>
        <a href="edit_subject.php?id=<?php echo $row['id']; ?>"
    class="btn btn-primary btn-sm">
    Edit
    </a>

    <a href="delete_subject.php?id=<?php echo $row['id']; ?>"
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

