<?php
include("db.php");

if(isset($_POST['register']))
{
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "INSERT INTO students(name,email,password)
            VALUES('$name','$email','$password')";

    if(mysqli_query($conn,$sql))
    {
        echo "<script>alert('Registration Successful');</script>";
    }
    else
{
    die("MySQL Error: " . mysqli_error($conn));
}
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Student Registration</title>
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

<div class="container">

<div class="row justify-content-center mt-5">
<div class="col-md-5">

<div class="login-card">

<h2 class="text-center mb-4">
📝 Create Account
</h2>

<form method="POST">

<label>Name</label>
<input type="text"
name="name"
class="form-control mb-3"
required>

<label>Email</label>
<input type="email"
name="email"
class="form-control mb-3"
required>

<label>Password</label>
<input type="password"
name="password"
class="form-control mb-3"
required>

<button type="submit"
name="register"
class="btn btn-success w-100">
Register
</button>

<div class="text-center mt-3">
Already have an account?
<a href="index.php">Login Here</a>
</div>

</form>

</div>

</div>
</div>

</div>

</body>
</html>