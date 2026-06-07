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
    $date = $_POST['study_date'];
    $hours = $_POST['study_hours'];

    $sql = "INSERT INTO study_plans
            (subject_name, study_date, study_hours, user_id)
            VALUES
            ('$subject', '$date', '$hours', '$user_id')";

    mysqli_query($conn, $sql);

    echo "<script>alert('Study Plan Saved Successfully');</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Study Planner</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>

    body{
        background:#f4f7fc;
    }

    .container{
        max-width:900px;
    }

    form{
        background:white;
        padding:20px;
        border-radius:15px;
        box-shadow:0 4px 15px rgba(0,0,0,0.08);
    }

    .table{
        background:white;
        box-shadow:0 4px 15px rgba(0,0,0,0.08);
    }

    </style>

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

<a href="planner.php" class="btn btn-warning btn-sm me-2">
📅 Planner
</a>

<a href="progress.php" class="btn btn-light btn-sm me-2">
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

    <h2>Study Planner</h2>

    <form method="POST">

        <label>Subject Name</label>

        <select name="subject_name" class="form-control mb-3" required>

            <option value="">Select Subject</option>

            <?php

            $subjects = mysqli_query($conn,
            "SELECT * FROM subjects WHERE user_id='$user_id'");

            if(mysqli_num_rows($subjects) > 0)
            {
                while($sub = mysqli_fetch_assoc($subjects))
                {
                    ?>
                    <option value="<?php echo $sub['subject_name']; ?>">
                        <?php echo $sub['subject_name']; ?>
                    </option>
                    <?php
                }
            }
            else
            {
                ?>
                <option value="">
                    No Subject Found
                </option>
                <?php
            }
            ?>

        </select>

        <label>Study Date</label>

        <input type="date"
               name="study_date"
               class="form-control mb-3"
               required>

        <label>Study Hours</label>

        <input type="number"
               name="study_hours"
               class="form-control mb-3"
               required>

        <button type="submit"
                name="save"
                class="btn btn-primary">
            Save Plan
        </button>

    </form>

    <h3 class="mt-5">All Study Plans</h3>

    <table class="table table-hover table-striped">

        <tr>
            <th>ID</th>
            <th>Subject Name</th>
            <th>Study Date</th>
            <th>Study Hours</th>
            <th>Action</th>
        </tr>

        <?php

        $result = mysqli_query($conn,
        "SELECT * FROM study_plans
         WHERE user_id='$user_id'
         ORDER BY id DESC");

        while($row = mysqli_fetch_assoc($result))
        {
        ?>

        <tr>

            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['subject_name']; ?></td>
            <td><?php echo date("d M Y", strtotime($row['study_date'])); ?></td>
            <td><?php echo $row['study_hours']; ?></td>

            <td>

                <a href="edit_plan.php?id=<?php echo $row['id']; ?>"
                   class="btn btn-primary btn-sm">
                    Edit
                </a>

                <a href="delete_plan.php?id=<?php echo $row['id']; ?>"
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