<?php
session_start();
include_once "../../connection.php";

$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($myconnection, $sql);
    
    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);

        if ($row['password'] == $password) {
           
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['name'];
            $_SESSION['email'] = $row['email'];
            
            exit;
        } else {
            $message = "Invalid password!";
        }
    } else {
        $message = "No user found with this email!";
    }
}

mysqli_close($myconnection);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
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
    .form-container h1 {
      text-align: center;
      margin-bottom: 25px;
      color: #333;
      font-weight: 600;
    }
    .form-group {
      position: relative;
    }
    .form-group .fa-envelope,
    .form-group .fa-lock {
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
    .btn-login {
      width: 100%;
      height: 50px;
      border-radius: 12px;
      font-weight: 500;
      background-color: #007bff;
      border: none;
      transition: background-color 0.3s ease;
    }
    .btn-login:hover {
      background-color: #0056b3;
    }
    .forgot-link {
      display: block;
      text-align: right;
      margin-top: 10px;
      font-size: 0.9rem;
    }
    .alert-info {
      margin-top: 20px;
      text-align: center;
    }
  </style>
</head>
<body>

<form class="form-container" method="POST" action="">
  <h1>Cafeteria Login</h1>

  <div class="form-group mb-4">
    <i class="fa-solid fa-envelope"></i>
    <input type="email" class="form-control" id="email" name="email" placeholder="Email address" required />
  </div>

  <div class="form-group mb-2">
    <i class="fa-solid fa-lock"></i>
    <input type="password" class="form-control" id="password" name="password" placeholder="Password" required />
  </div>

  <a href="forget-password.php" class="forgot-link">Forgot password?</a>

  <button type="submit" class="btn btn-primary btn-login mt-4">Login</button>

  <?php if (!empty($message)): ?>
    <div class="alert alert-info"><?php echo $message; ?></div>
  <?php endif; ?>
</form>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>