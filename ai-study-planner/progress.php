<?php
session_start();

if(!isset($_SESSION['email']))
{
    header("Location: index.php");
    exit();
}
include("db.php");

if(isset($_POST['save']))
{
    $subject = $_POST['subject_name'];
    $completed = $_POST['completed_units'];
    $total = $_POST['total_units'];

   $user_id = $_SESSION['user_id'];

mysqli_query($conn,
"INSERT INTO progress (subject_name,completed_units,total_units,user_id)
VALUES
('$subject','$completed','$total','$user_id')");

}
?>

<!DOCTYPE html>
<html>
<head>
<title>Progress Tracker</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow">
<div class="container">



</div>
</nav>
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

<a href="progress.php" class="btn btn-warning btn-sm me-2">
📊 Progress
</a>

<a href="logout.php"
class="btn btn-danger btn-sm">
🚪 Logout
</a>

</div>

</div>
</nav>


<div class="container mt-4">

<h2>Progress Tracker</h2>

<form method="post">

<div class="mb-3">
<label>Subject Name</label>
<select name="subject_name" class="form-control" required>

<option value="">Select Subject</option>

<?php

$user_id = $_SESSION['user_id'];

$subjects = mysqli_query($conn,
"SELECT * FROM subjects WHERE user_id='$user_id'");

while($sub = mysqli_fetch_assoc($subjects))
{
?>

<option value="<?php echo $sub['subject_name']; ?>">
<?php echo $sub['subject_name']; ?>
</option>

<?php
}
?>

</select>
</div>

<div class="mb-3">
<label>Completed Units</label>
<input type="number" name="completed_units" class="form-control" required>
</div>

<div class="mb-3">
<label>Total Units</label>
<input type="number" name="total_units" class="form-control" required>
</div>

<button type="submit" name="save" class="btn btn-success">
Save Progress
</button>

</form>

<h3 class="mt-5">All Progress Records</h3>

<input type="text"
id="searchProgress"
class="form-control mb-3"
placeholder="🔍 Search Progress...">

<table class="table table-hover table-striped" id="progressTable">

<tr>
<th>Subject</th>
<th>Completed</th>
<th>Total</th>
<th>Progress</th>
<th>Action</th>
</tr>

<?php

$user_id = $_SESSION['user_id'];

$result = mysqli_query($conn,
"SELECT * FROM progress
WHERE user_id='$user_id'");

while($row = mysqli_fetch_assoc($result))
{
    $percent =
    round(($row['completed_units'] /
    $row['total_units']) * 100);
?>

<tr>

<td><?php echo $row['subject_name']; ?></td>

<td><?php echo $row['completed_units']; ?></td>

<td><?php echo $row['total_units']; ?></td>

<td><?php echo $percent; ?>%</td>

<td>

<a href="edit_progress.php?id=<?php echo $row['id']; ?>"
class="btn btn-primary btn-sm">
Edit
</a>

<a href="delete_progress.php?id=<?php echo $row['id']; ?>"
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

<script>

document.getElementById("searchProgress")
.addEventListener("keyup", function() {

let filter = this.value.toLowerCase();

let rows =
document.querySelectorAll("#progressTable tr");

rows.forEach(function(row,index){

if(index===0) return;

let text =
row.innerText.toLowerCase();

row.style.display =
text.includes(filter) ? "" : "none";

});

});

</script>
</body>
</html>