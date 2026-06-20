<?php
session_start();

date_default_timezone_set('Asia/Kolkata');

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

if($progress < 40)
{
    $progressColor = "bg-danger";
}
elseif($progress < 70)
{
    $progressColor = "bg-warning";
}
else
{
    $progressColor = "bg-success";
}

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

<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    
<title>🤖 AI Study Planner </title>

<link rel="icon"
href="https://cdn-icons-png.flaticon.com/512/3135/3135755.png">

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
    box-shadow:0 8px 25px rgba(0,0,0,0.15);
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

<a class="navbar-brand fw-bold" href="#">
🤖 AI Study Planner 
</a>

<button onclick="toggleDarkMode()" class="btn btn-dark me-2">
🌙 Dark Mode
</button>

<a href="logout.php" class="btn btn-danger">
Logout
</a>

</div>
</nav>

<div class="container-fluid px-5 mt-5">

<?php

$hour = date("H");

if($hour >= 5 && $hour < 12)
{
    $greeting = "🌅 Good Morning";
}
elseif($hour >= 12 && $hour < 17)
{
    $greeting = "☀️ Good Afternoon";
}
elseif($hour >= 17 && $hour < 21)
{
    $greeting = "🌇 Good Evening";
}
else
{
    $greeting = "🌙 Good Night";
}


?>


<h2 class="fw-bold">
<?php echo $greeting; ?>,
<?php echo $_SESSION['name']; ?>
</h2>

<div class="card mt-4">
    <div class="card-body">

        <h4>👤 Student Profile</h4>

          <div class="text-center mb-3">
<img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png"
style="
width:120px;
border-radius:50%;
box-shadow:0 0 25px #0d6efd;
">
</div>

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

<div class="card mt-3 bg-primary text-white">
    <div class="card-body text-center">


        <h2>🤖 AI Study Planner </h2>
        <p class="mt-2">
🎯 Goal: Crack Exams With Smart Planning
</p>
<p>
📈 Track Progress • 📚 Manage Subjects • 📝 Exam Preparation
</p>

        <h4>
            📚 <?php echo $totalSubjects; ?> Subjects |
            📅 <?php echo $totalPlans; ?> Plans |
            🎯 <?php echo $progress; ?>% Progress
        </h4>

        <h5>
📅 <?php echo date("d M Y"); ?>
</h5>

<h5 id="clock"></h5>

    </div>
</div>




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

<div class="card mt-4">
<div class="card-body text-center">

<h4>⚡ Quick Actions</h4>



<a href="subjects.php" class="btn btn-primary m-2">
➕ Add Subject
</a>

<a href="planner.php" class="btn btn-success m-2">
📅 Add Study Plan
</a>

<a href="progress.php" class="btn btn-warning m-2">
📈 Update Progress
</a>

<a href="exam.php" class="btn btn-danger m-2">
📝 Add Exam
</a>

<a href="certificate.php" class="btn btn-success">
🎓 Generate Certificate
</a>

<div class="card mt-4">
<div class="card-body">

<h4>🤖 AI Study Assistant</h4>

<input type="text"
class="form-control"
id="userQuestion"
placeholder="Ask study related question...">

<button class="btn btn-primary mt-2"
onclick="askAI()">
Ask AI
</button>

<div id="aiAnswer" class="alert alert-info mt-3">
AI response will appear here...
</div>

</div>
</div>

</div>
</div>

<div class="card mt-3">
    <div class="card-body text-center">

        <h4>🎯 Current Goal</h4>

        <div class="alert alert-primary">

            Complete all subjects before exam and maintain daily study streak.

        </div>

    </div>
</div>



<div class="row mt-4">

<div class="col-md-3">
<div class="card text-center h-100 shadow border-0 bg-primary text-white">
<div class="card-body">
<h2>📝</h2>    
<h3><?php echo $totalSubjects; ?></h3>
<p>Total Subjects</p>
</div>
</div>
</div>

<div class="col-md-3">    
<div class="card text-center h-100 shadow border-0 bg-success text-white">
<div class="card-body">
<h2>📝</h2>    
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
<div class="card text-center h-100 shadow border-0 bg-danger text-white">
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


<h2 class="fw-bold text-primary">
<?php echo $progress; ?>%
</h2>
<p>Progress</p>

<div class="progress mt-3">
<div class="progress-bar <?php echo $progressColor; ?>"
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
    <div class="card-body text-center">

        <h4>💡 Quote of the Day</h4>

        <div class="alert alert-success" id="quoteBox">
    Loading Quote...
</div>

 <div class="card mt-3">
    <div class="card-body text-center">

        <h4>📚 Daily Study Tip</h4>

        <div class="alert alert-info" id="tipBox">
            Loading Tip...
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

<div class="progress mt-3">

<div class="progress-bar bg-success progress-bar-striped progress-bar-animated"

style="width: <?php echo $readinessScore; ?>%">

<?php echo $readinessScore; ?>%

</div>

</div>



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
<div class="card mt-4">
<div class="card-body">

<div class="card mt-4" style="max-width:500px; margin:auto;">
<div class="card-body text-center">

<h4>🥧 Progress Distribution</h4>

<div style="width:280px; height:280px; margin:auto;">
<canvas id="pieChart"></canvas>
</div>

</div>
</div>
</div>

</div>
</div>

<div class="card mt-4">
<div class="card-body">

<h3 class="text-primary">📊 Subject Wise Progress</h3>

<div style="height:350px;">
<canvas id="subjectChart"></canvas>
</div>

</div>
</div>

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
    data: <?php echo json_encode($trendHours); ?>,

    borderColor: '#0d6efd',
    backgroundColor: 'rgba(13,110,253,0.2)',

    fill: true,
    tension: 0.4,

    pointRadius: 6,
    pointHoverRadius: 8,

    pointBackgroundColor: '#0d6efd',
    pointBorderColor: '#ffffff',
    pointBorderWidth: 2
}]
    },
   options: {
    responsive: true,

    plugins:{
        legend:{
            display:true
        }
    },

    scales:{
        y:{
            beginAtZero:true
        }
    }
}
});

const pieCtx =
document.getElementById('pieChart');

new Chart(pieCtx, {
    type: 'pie',
    data: {
        labels: ['Completed','Remaining'],
        datasets: [{
            data: [
                <?php echo $completedPercent; ?>,
                <?php echo $remainingPercent; ?>
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
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

<footer class="bg-dark text-white text-center p-4 mt-5">

<h4>🤖 AI Study Planner </h4>

<p>
Major Project | Diploma CSE
</p>
<footer class="bg-dark text-white text-center p-3 mt-5">
© 2026 AI Study Planner | Developed By Sunder Kumar & Naveen Tripathi
</footer> 



<script>
setInterval(function()
{
    document.getElementById("clock").innerHTML =
    "🕒 " + new Date().toLocaleTimeString();
},1000);
</script>

<script>

const quotes = [

"Success is the sum of small efforts repeated daily.",
"Push yourself because no one else will do it for you.",
"Dream big. Start small. Act now.",
"Discipline beats motivation every time.",
"Every day is a chance to improve yourself.",
"Small progress is still progress.",
"Study now, shine later.",
"Your future is created by what you do today.",
"Consistency is the key to success.",
"Don't stop until you're proud.",
"Hard work today creates success tomorrow.",
"Stay focused and never give up.",
"Learning never exhausts the mind.",
"Winners are not afraid of failure.",
"Believe in yourself and keep moving forward.",
"The expert was once a beginner.",
"Success starts with self-discipline.",
"One chapter at a time, one step closer.",
"Great things take time and patience.",
"Your only limit is your determination."

];

let today = new Date();
let dayNumber = Math.floor(today.getTime() / (1000 * 60 * 60 * 24));

let quoteIndex = dayNumber % quotes.length;

document.getElementById("quoteBox").innerHTML =
"💡 " + quotes[quoteIndex];

const tips = [

"Study for 25 minutes and take a 5 minute break.",
"Revise yesterday's topics before learning new ones.",
"Make short notes for quick revision.",
"Practice previous year questions regularly.",
"Keep your phone away while studying.",
"Focus on one subject at a time.",
"Use active recall instead of rereading.",
"Teach what you learn to someone else.",
"Set daily study goals.",
"Maintain a fixed study schedule.",
"Highlight only important points.",
"Take handwritten notes.",
"Use diagrams and flowcharts.",
"Study difficult subjects first.",
"Sleep at least 7 hours daily.",
"Revise weekly to avoid forgetting.",
"Practice coding every day.",
"Attempt mock tests regularly.",
"Track your daily progress.",
"Consistency beats intensity."

];

let tipIndex = dayNumber % tips.length;

document.getElementById("tipBox").innerHTML =
"📖 " + tips[tipIndex];

</script>

<script>

<?php if($progress >= 100){ ?>

confetti({
    particleCount: 200,
    spread: 180,
    origin: { y: 0.6 }
});

<?php } ?>

</script>

<script>

function askAI()
{
let question =
document.getElementById("userQuestion").value;

let answer="";

if(question.includes("java"))
{
answer="📘 Focus on JDBC, Servlet and JSP.";
}
else if(question.includes("dbms"))
{
answer="🗄️ Revise Normalization and SQL Queries.";
}
else if(question.includes("cloud"))
{
answer="☁️ Learn IaaS, PaaS and SaaS.";
}
else
{
answer="📚 Keep studying regularly and revise daily.";
}

document.getElementById("aiAnswer").innerHTML=answer;
}

</script>



</body>
</html>