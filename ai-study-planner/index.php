<?php
session_start();
include("db.php");

if(isset($_POST['login']))
{
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM students
            WHERE email='$email'
            AND password='$password'";

    $result = mysqli_query($conn,$sql);

    if(mysqli_num_rows($result) > 0)
    {
        $user = mysqli_fetch_assoc($result);

        $_SESSION['name'] = $user['name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['user_id'] = $user['id'];

        header("Location: dashboard.php");
        exit();
    }
    else
    {
        echo "<script>alert('Invalid Email or Password');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>

body{
    background:linear-gradient(135deg,#4facfe,#00f2fe);
    min-height:100vh;
}

.login-card{
    background:white;
    padding:30px;
    border-radius:20px;
    box-shadow:0 8px 25px rgba(0,0,0,0.15);
}

</style>
</head>
<body>

<div class="container mt-5">

    <h2 class="text-center mb-4">
🤖 AI Study Planner Login
</h2>

   <div class="row justify-content-center mt-5">
<div class="col-md-5">
<div class="login-card">

    <form method="POST">

        <label>Email</label>
        <input type="email" name="email" class="form-control mb-3" required>

        <label>Password</label>
        <input type="password" name="password" class="form-control mb-3" required>

        <button type="submit" name="login" class="btn btn-primary w-100">
            Login
        </button>
        <div class="text-center mt-3">
    Don't have an account?
    <a href="register.php">Register Here</a>
</div>

    </form>

</div>
</div>
</div>

</body>
</html>