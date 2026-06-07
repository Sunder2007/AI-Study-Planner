<?php
session_start();

if(!isset($_SESSION['email']))
{
    header("Location: index.php");
    exit();
}

include("db.php");

$user_id = $_SESSION['user_id'];

$result = mysqli_query($conn,
"SELECT COUNT(*) as total
FROM subjects
WHERE user_id='$user_id'");

$resultStreak = mysqli_query($conn,"
SELECT COUNT(DISTINCT study_date) as days
FROM study_plans
WHERE user_id='$user_id'
");

$rowStreak = mysqli_fetch_assoc($resultStreak);

$streak = $rowStreak['days'];

$row = mysqli_fetch_assoc($result);
$totalSubjects = $row['total'];

$result2 = mysqli_query($conn,
"SELECT COUNT(*) as total
FROM study_plans
WHERE user_id='$user_id'");

$row2 = mysqli_fetch_assoc($result2);
$totalPlans = $row2['total'];

$user_id = $_SESSION['user_id'];

$result3 = mysqli_query($conn,"
SELECT SUM(completed_units) as completed,
SUM(total_units) as total
FROM progress
WHERE user_id='$user_id'");

$row3 = mysqli_fetch_assoc($result3);

if($row3['total'] > 0)
{
    $progress = round(($row3['completed'] / $row3['total']) * 100);
}
else
{
    $progress = 0;
}
$completedPercent = $progress;
$remainingPercent = 100 - $progress;

if($progress == 0)
{
    $readinessScore = 0;
    $status = "No Data Available";
}
elseif($progress < 40)
{
    $readinessScore = 40;
    $status = "Needs Improvement";
}
elseif($progress < 70)
{
    $readinessScore = 70;
    $status = "Good";
}
else
{
    $readinessScore = 100;
    $status = "Exam Ready";
}

if($progress == 0)
{
    $recommendation = "No study data found. Start adding subjects, plans and progress.";
}
elseif($progress < 40)
{
    $recommendation = "Focus on weak subjects. Study at least 2 extra hours daily.";
}
elseif($progress < 70)
{
    $recommendation = "Good progress. Continue regular study and complete pending units.";
}
else
{
    $recommendation = "Excellent progress. Start revision and practice previous year papers.";
}
if($progress < 30)
{
    $badge = "🏅 Beginner Learner";
}
elseif($progress < 70)
{
    $badge = "🥈 Consistent Learner";
}
else
{
    $badge = "🥇 Exam Ready";
}

if($progress < 25)
{
    $rank = "🥉 Bronze Learner";
}
elseif($progress < 50)
{
    $rank = "🥈 Silver Learner";
}
elseif($progress < 75)
{
    $rank = "🥇 Gold Learner";
}
else
{
    $rank = "👑 Platinum Learner";
}
$points = ($progress * 10) + ($streak * 20);

 $examDate = "2026-06-30";

$today = date("Y-m-d");

$daysLeft = floor(
(strtotime($examDate) - strtotime($today))
/ (60 * 60 * 24)
);

$chartLabels = [];
$chartData = [];

$trendDates = [];
$trendHours = [];

$trendQuery = mysqli_query($conn,"
SELECT study_date,
SUM(study_hours) as hours
FROM study_plans
WHERE user_id='$user_id'
GROUP BY study_date
ORDER BY study_date ASC");

while($trendRow = mysqli_fetch_assoc($trendQuery))
{
    $trendDates[] = $trendRow['study_date'];
    $trendHours[] = $trendRow['hours'];
}

$chartQuery = mysqli_query($conn,"
SELECT subject_name,
ROUND((completed_units/total_units)*100) as percentage
FROM progress
WHERE user_id='$user_id'");

while($chartRow = mysqli_fetch_assoc($chartQuery))
{
    $chartLabels[] = $chartRow['subject_name'];
    $chartData[] = $chartRow['percentage'];
}

$weeklyHours = 0;
$dailyGoal =10;

$todayHours = 0;

$todayQuery = mysqli_query($conn,"
SELECT SUM(study_hours) as total
FROM study_plans
WHERE user_id='$user_id'
AND study_date = CURDATE()");

$todayRow = mysqli_fetch_assoc($todayQuery);

$todayHours = $todayRow['total'];

if(!$todayHours)
{
    $todayHours = 0;
}

$goalPercent = min(
round(($todayHours / $dailyGoal) * 100),
100
);

$weeklyQuery = mysqli_query($conn,"
SELECT SUM(study_hours) as totalHours
FROM study_plans
WHERE user_id='$user_id'");

$weeklyRow = mysqli_fetch_assoc($weeklyQuery);

$weeklyHours = $weeklyRow['totalHours'];

if(!$weeklyHours)
{
    $weeklyHours = 0;
}
$weakQuery = mysqli_query($conn,"
SELECT subject_name,
ROUND((completed_units/total_units)*100) as percentage
FROM progress
WHERE user_id='$user_id'
ORDER BY percentage ASC
LIMIT 1");

$weakRow = mysqli_fetch_assoc($weakQuery);

if($weakRow)
{
    $weakSubject = $weakRow['subject_name'];
    $weakPercentage = $weakRow['percentage'];
}
else
{
    $weakSubject = "No Subject";
    $weakPercentage = 0;
}

if($weakPercentage < 50 && $weakSubject != "No Subject")
{
    $alerts[] = "⚠ $weakSubject progress below 50%";
}

if($daysLeft <= 30)
{
    $alerts[] = "📅 Exam in $daysLeft days";
}

if($streak > 0)
{
    $alerts[] = "🔥 Study streak active: $streak days";
}

if($todayHours < $dailyGoal)
{
    $alerts[] = "🎯 Daily goal incomplete today";
}

?>


<!DOCTYPE html>
<html>
<head>
<title>AI Study Planner Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>

body{
    background:#f4f7fc;
}

.card{
    border:none;
    border-radius:15px;
    box-shadow:0 4px 15px rgba(0,0,0,0.08);
    transition:0.3s;
}

.card:hover{
    transform:translateY(-5px);
}

canvas{
    background:white;
    padding:15px;
    border-radius:15px;
    box-shadow:0 4px 15px rgba(0,0,0,0.08);
}

h3,h4{
    font-weight:600;
}

</style>

</head>
<body>

<nav class="navbar navbar-dark bg-primary">
<div class="container">

<a class="navbar-brand" href="#">
AI Study Planner
</a>

<button onclick="toggleDarkMode()" class="btn btn-dark me-2">
🌙 Dark Mode
</button>

<a href="logout.php" class="btn btn-danger">
Logout
</a>

</div>
</nav>

<div class="container mt-5">

<h2 class="fw-bold">
👋 Welcome, <?php echo $_SESSION['name']; ?>
</h2>
<div class="card mt-4">
    <div class="card-body">

        <h4>👤 Student Profile</h4>

        <p><b>Full Name:</b> <?php echo $_SESSION['name']; ?></p>

        <p><b>Email:</b> <?php echo $_SESSION['email']; ?></p>

        <p><b>Rank:</b> <?php echo $rank; ?></p>

        <p><b>Total Points:</b> <?php echo $points; ?></p>

    </div>
</div>
<p>
Logged in as:
<b><?php echo $_SESSION['email']; ?></b>
</p>

<a href="dashboard.php" class="btn btn-warning btn-sm me-2">
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

<a href="exam.php" class="btn btn-info ms-2">
Exam Timetable
</a>

<div class="row mt-4">

<div class="col-md-3">    
<div class="card text-center h-100 shadow border-0">
<div class="card-body">
<h3><?php echo $totalSubjects; ?></h3>
<p>Total Subjects</p>
</div>
</div>
</div>

<div class="col-md-3">    
<div class="card text-center h-100 shadow border-0">
<div class="card-body">
<h3><?php echo $totalPlans; ?></h3>
<p>Study Plans</p>
</div>
</div>
</div>
<?php

$examCount = mysqli_fetch_assoc(
mysqli_query($conn,
"SELECT COUNT(*) as total
FROM exams
WHERE user_id='$user_id'
AND exam_date >= CURDATE()")
);

?>

<div class="col-md-3">    
<div class="card text-center h-100 shadow border-0">
<div class="card-body text-center">

<h2>📝</h2>

<h3>
<?php echo $examCount['total']; ?>
</h3>

<p>Total Upcoming Exams</p>

</div>
</div>
</div>

<div class="col-md-3">    
<div class="card text-center h-100 shadow border-0">
<div class="card-body text-center">


<h3><?php echo $progress; ?>%</h3>
<p>Progress</p>

<div class="progress mt-3">
<div class="progress-bar bg-success"
role="progressbar"
style="width: <?php echo $progress; ?>%">
<?php echo $progress; ?>%
</div>
</div>

</div>
</div>
</div>

</div>
<div class="card mt-4">
    <div class="card-body">

        <h4>🤖 AI Study Recommendation</h4>

        <div class="alert alert-info mt-3">
            <?php echo $recommendation; ?>
        </div>

    </div>
</div>

<div class="card mt-4">
    <div class="card-body">

        <h4>🔔 Smart Notifications</h4>

        <?php
        foreach($alerts as $alert)
        {
            echo "<div class='alert alert-warning mt-2'>$alert</div>";
        }
        ?>

    </div>
</div>

<div class="card mt-4">
    <div class="card-body">

        <h4>🎯 Exam Readiness Score</h4>
        <h2 class="text-primary">
    <?php echo $readinessScore; ?>/100
</h2>



       <div class="alert alert-success">
    Status: <?php echo $status; ?>
</div>

</div>
</div>
<div class="card mt-4">
    <div class="card-body">
        

        <h4>⏳ Exam Countdown</h4>

        <h2 class="text-danger">
            <?php echo $daysLeft; ?> Days Left
        </h2>

        <p>
            Exam Date:
            <?php echo date("d M Y", strtotime($examDate)); ?>
        </p>

    </div>
</div>

<div class="card mt-4">
    <div class="card-body">

        <h4>📊 Weekly Study Report</h4>

        <h3 class="text-success">
            <?php echo $weeklyHours; ?> Hours
        </h3>

        <p>Total Study Hours Recorded</p>

    </div>
</div>

<div class="card mt-4">
    <div class="card-body">

        <h4>🔥 Study Streak</h4>

        <h2 class="text-warning">
            <?php echo $streak; ?> Days
        </h2>

        <p>
            Keep studying daily to increase your streak.
        </p>

    </div>
</div>
<div class="card mt-4">
    <div class="card-body">

        <h4>🎯 Daily Goal Tracker</h4>

        <h5>
            Goal:
            <?php echo $dailyGoal; ?> Hours
        </h5>

        <h5 class="text-success">
            Studied Today:
            <?php echo $todayHours; ?> Hours
        </h5>

        <div class="progress mt-3">

            <div class="progress-bar bg-primary"
            style="width: <?php echo $goalPercent; ?>%">

                <?php echo $goalPercent; ?>%

            </div>

        </div>

        <p class="mt-2">

        <?php

        if($todayHours >= $dailyGoal)
        {
            echo "✅ Daily Goal Achieved!";
        }
        else
        {
            echo "📚 Keep studying to reach your goal.";
        }

        ?>

        </p>

    </div>
</div>
<div class="card mt-4">
    <div class="card-body">

        <h4>🏆 Achievement Badge</h4>

        <h2 class="text-warning">
            <?php echo $badge; ?>
        </h2>

        <p>
            Based on your overall study progress.
        </p>

    </div>
</div>
<div class="card mt-4">
    <div class="card-body">

        <h4>🏆 Leaderboard Rank</h4>

        <h2 class="text-primary">
            <?php echo $rank; ?>
        </h2>

        <p>
            Rank calculated from your overall progress.
        </p>

    </div>
</div>

<div class="card mt-4">
    <div class="card-body">

        <h4>⭐ Total Points</h4>

        <h2 class="text-success">
            <?php echo $points; ?>
        </h2>

        <p>
            Earn points by maintaining study progress and streak.
        </p>

    </div>
</div>

<div class="card mt-4">
    <div class="card-body">

        <h4>⚠ Weak Subject Detection</h4>

        <h2 class="text-danger">
            <?php echo $weakSubject; ?>
        </h2>

        <p>
            Progress:
            <?php echo $weakPercentage; ?>%
        </p>

        <div class="alert alert-warning">
            Focus more on this subject to improve your overall score.
        </div>

    </div>
</div>


 <h3 class="mt-5">📚 Subject Completion Status</h3>

<?php

$subjectProgress = mysqli_query($conn,"
SELECT subject_name,
ROUND((completed_units/total_units)*100) as percentage
FROM progress
WHERE user_id='$user_id'");

while($sp = mysqli_fetch_assoc($subjectProgress))
{
?>

<div class="card mt-3">
<div class="card-body">

<h5>
<?php echo $sp['subject_name']; ?>
</h5>

<div class="progress">

<div class="progress-bar bg-success"
style="width: <?php echo $sp['percentage']; ?>%">
<?php echo $sp['percentage']; ?>%
</div>

</div>

</div>
</div>

<?php
}
?>       
<h3 class="mt-5">Recent Study Plans</h3>

<table class="table table-hover table-striped">
<tr>
<th>Subject</th>
<th>Date</th>
<th>Hours</th>
</tr>

<?php
$result4 = mysqli_query($conn,
"SELECT * FROM study_plans
WHERE user_id='$user_id'
ORDER BY id DESC
LIMIT 5");

while($row4 = mysqli_fetch_assoc($result4))
{
?>
<tr>
<td><?php echo $row4['subject_name']; ?></td>
<td>
<?php echo date("d M Y", strtotime($row4['study_date'])); ?>
</td>
<td><?php echo $row4['study_hours']; ?></td>
</tr>
<?php
}
?>

</table>

<h3 class="mt-5 text-primary">
📊 Study Analytics Dashboard
</h3>
<h3 class="mt-5">🥧 Progress Distribution</h3>

<canvas id="pieChart"></canvas>

<h3 class="mt-5">Subject Wise Progress</h3>

<canvas id="subjectChart"></canvas>

<h3 class="mt-5">📈 Progress Trend</h3>

<canvas id="trendChart"></canvas>

<canvas id="myChart"></canvas>

</div>

<script>
const ctx = document.getElementById('myChart');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Subjects', 'Plans', 'Progress'],
        datasets: [{
            label: 'Study Statistics',
            data: [
                <?php echo $totalSubjects; ?>,
                <?php echo $totalPlans; ?>,
                <?php echo $progress; ?>
            ]
        }]
    }
});

const subjectCtx =
document.getElementById('subjectChart');

new Chart(subjectCtx, {
    type: 'bar',
    data: {
        labels:
        <?php echo json_encode($chartLabels); ?>,

        datasets: [{
            label: 'Progress %',
            data:
            <?php echo json_encode($chartData); ?>,
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true,
                max: 100
            }
        }
    }
});
const trendCtx =
document.getElementById('trendChart');

new Chart(trendCtx, {
    type: 'line',
    data: {
        labels:
        <?php echo json_encode($trendDates); ?>,

        datasets: [{
            label: 'Study Hours',
            data:
            <?php echo json_encode($trendHours); ?>,
            fill: false,
            tension: 0.3
        }]
    },
    options: {
        responsive: true
    }
});

const pieCtx =
document.getElementById('pieChart');

new Chart(pieCtx, {
    type: 'pie',
    data: {
        labels: [
            'Completed',
            'Remaining'
        ],
        datasets: [{
            data: [
                <?php echo $completedPercent; ?>,
                <?php echo $remainingPercent; ?>
            ]
        }]
    },
    options: {
        responsive: true
    }
});

</script>


<script>

function toggleDarkMode()
{
    document.body.classList.toggle("bg-dark");
    document.body.classList.toggle("text-white");

    let cards = document.querySelectorAll(".card");

    cards.forEach(function(card)
    {
        card.classList.toggle("bg-secondary");
        card.classList.toggle("text-white");
    });

    let tables = document.querySelectorAll(".table");

    tables.forEach(function(table)
    {
        table.classList.toggle("table-dark");
    });

    if(document.body.classList.contains("bg-dark"))
    {
        localStorage.setItem("darkMode","enabled");
    }
    else
    {
        localStorage.setItem("darkMode","disabled");
    }
}

window.onload = function()
{
    if(localStorage.getItem("darkMode") === "enabled")
    {
        document.body.classList.add("bg-dark");
        document.body.classList.add("text-white");

        let cards = document.querySelectorAll(".card");

        cards.forEach(function(card)
        {
            card.classList.add("bg-secondary");
            card.classList.add("text-white");
        });

        let tables = document.querySelectorAll(".table");

        tables.forEach(function(table)
        {
            table.classList.add("table-dark");
        });
    }
}

</script>

</body>
</html>