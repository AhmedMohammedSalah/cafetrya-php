<?php

session_start();
include_once "../../connection.php";
$message = "";
$messageType = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (isset($_POST['email']) && isset($_POST['new_password']) && isset($_POST['confirm_password'])) {
        $email = mysqli_real_escape_string($myconnection, $_POST['email']);
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];
        
     
        $sql = "SELECT * FROM users WHERE email='$email'";
        $result = mysqli_query($myconnection, $sql);
        
        if (mysqli_num_rows($result) == 1) {
           
            if (strlen($newPassword) < 6) {
                $message = "Password must be at least 6 characters long.";
                $messageType = "warning";
            } elseif ($newPassword !== $confirmPassword) {
                $message = "Passwords do not match.";
                $messageType = "warning";
            } else {
                
                $updateSql = "UPDATE users SET password='$newPassword' WHERE email='$email'";
                if (mysqli_query($myconnection, $updateSql)) {
                    $message = "Password has been updated successfully. <a href='index.php'>Login now</a>";
                    $messageType = "success";
                } else {
                    $message = "Error updating password: " . mysqli_error($myconnection);
                    $messageType = "danger";
                }
            }
        } else {
            $message = "No account found with that email address.";
            $messageType = "warning";
        }
    }
}
mysqli_close($myconnection);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Reset Password</title>
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
    .btn-reset {
      width: 100%;
      height: 50px;
      border-radius: 12px;
      font-weight: 500;
      background-color: #007bff;
      border: none;
      transition: background-color 0.3s ease;
    }
    .btn-reset:hover {
      background-color: #0056b3;
    }
    .back-link {
      display: block;
      text-align: center;
      margin-top: 20px;
      font-size: 0.9rem;
    }
  </style>
</head>
<body>
<div class="form-container">
  <h1>Reset Password</h1>
  <p class="text-center mb-4">Enter your email and new password.</p>
  
  <?php if (!empty($message)): ?>
    <div class="alert alert-<?php echo $messageType; ?>"><?php echo $message; ?></div>
  <?php endif; ?>
  
  <form method="POST" action="">
    <div class="form-group mb-4">
      <i class="fa-solid fa-envelope"></i>
      <input type="email" class="form-control" id="email" name="email" placeholder="Email address" required />
    </div>
    <div class="form-group mb-4">
      <i class="fa-solid fa-lock"></i>
      <input type="password" class="form-control" id="new_password" name="new_password" placeholder="New password" required />
    </div>
    <div class="form-group mb-4">
      <i class="fa-solid fa-lock"></i>
      <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm new password" required />
    </div>
    <button type="submit" class="btn btn-primary btn-reset">Update Password</button>
  </form>
  
  <a href="index.php" class="back-link">Back to Login</a>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>