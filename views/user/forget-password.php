<?php
include_once  "../../connection.php";
$connection =$myconnection;
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = mysqli_real_escape_string($connection, $_POST['email']);

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($connection, $sql);

    if (mysqli_num_rows($result) === 1) {
        
        $message = " A password reset link would be sent to your email.";
    } else {
        $message = " No user found with that email.";
    }
}

mysqli_close($connection);

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Forgot Password</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />

  <style>
    body {
      background: linear-gradient(to right, #74ebd5, #acb6e5);
      height: 100vh;
      margin: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .form-container {
      background-color: #ffffff;
      padding: 40px 30px;
      border-radius: 20px;
      box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
      width: 100%;
      max-width: 420px;
      transition: transform 0.3s ease-in-out;
    }

    .form-container:hover {
      transform: scale(1.01);
    }

    .form-container h3 {
      text-align: center;
      margin-bottom: 25px;
      color: #333;
      font-weight: 600;
    }

    .form-container p {
      text-align: center;
      color: #6c757d;
      margin-bottom: 30px;
    }

    .form-group {
      position: relative;
    }

    .form-group .fa-envelope {
      position: absolute;
      left: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: #aaa;
    }

    .form-control {
      padding-left: 40px;
      height: 50px;
      border-radius: 10px;
      border: 1px solid #ddd;
      transition: 0.3s;
    }

    .form-control:focus {
      border-color: #007bff;
      box-shadow: 0 0 0 4px rgba(0,123,255,0.1);
    }

    .btn-submit {
      width: 100%;
      height: 50px;
      border-radius: 12px;
      font-weight: 500;
      background-color: #007bff;
      border: none;
      transition: background-color 0.3s ease;
    }

    .btn-submit:hover {
      background-color: #0056b3;
    }

    .back-link {
      display: block;
      text-align: center;
      margin-top: 20px;
      color: #007bff;
      text-decoration: none;
      transition: color 0.2s ease;
    }

    .back-link:hover {
      color: #0056b3;
      text-decoration: underline;
    }
  </style>
</head>
<body>

  <form class="form-container" method="POST" action="">
    <h3>Forgot Password</h3>
    <p>Enter your email and we'll send you instructions to reset your password.</p>

    <div class="form-group mb-4">
      <i class="fa-solid fa-envelope"></i>
      <input type="email" class="form-control" name="email" placeholder="Email address" >
    </div>

    <button type="submit" class="btn btn-primary btn-submit">Send Reset Link</button>

    <a href="login.php" class="back-link">Back to Login</a>
  </form>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>