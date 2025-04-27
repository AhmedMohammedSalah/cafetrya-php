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
            header("Location: home.php");
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
      background: rgb(113, 80, 36);
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
      border-color: rgb(113, 80, 36);
      box-shadow: 0 0 0 4px rgba(0,123,255,0.1);
    }
    .is-invalid {
      border-color: #dc3545 !important;
    }
    .is-valid {
      border-color: #198754 !important;
    }
    .error-message {
      color: #dc3545;
      font-size: 0.85rem;
      margin-top: 5px;
      margin-left: 15px;
      display: none;
    }
    .btn-login {
      width: 100%;
      height: 50px;
      border-radius: 12px;
      font-weight: 500;
      background-color:rgb(113, 80, 36);
      border: none;
      transition: background-color 0.3s ease;
    }
    .btn-login:hover {
      background-color: rgb(113, 80, 36);;
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
<form class="form-container" method="POST" action="" id="loginForm">
  <h1>Cafeteria Login</h1>
  <div class="form-group mb-4">
    <i class="fa-solid fa-envelope"></i>
    <input type="email" class="form-control" id="email" name="email" placeholder="Email address" />
    <div class="error-message" id="emailError">Please enter a valid email address.</div>
  </div>
  <div class="form-group mb-2">
    <i class="fa-solid fa-lock"></i>
    <input type="password" class="form-control" id="password" name="password" placeholder="Password" />
    <div class="error-message" id="passwordError">Password must be at least 6 characters long.</div>
  </div>
  <a href="forget-password.php" class="forgot-link">Forgot password?</a>
  <button type="submit" class="btn btn-primary btn-login mt-4">Login</button>
  <?php if (!empty($message)): ?>
    <div class="alert alert-info mt-3"><?php echo $message; ?></div>
  <?php endif; ?>
</form>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('loginForm');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const emailError = document.getElementById('emailError');
    const passwordError = document.getElementById('passwordError');
    
  
    function validateEmail(email) {
      const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
      return re.test(String(email).toLowerCase());
    }
    
 
    emailInput.addEventListener('input', function() {
      if (this.value.trim() === '') {
        this.classList.remove('is-valid');
        this.classList.add('is-invalid');
        emailError.textContent = 'Email address is required.';
        emailError.style.display = 'block';
      } else if (!validateEmail(this.value)) {
        this.classList.remove('is-valid');
        this.classList.add('is-invalid');
        emailError.textContent = 'Please enter a valid email address.';
        emailError.style.display = 'block';
      } else {
        this.classList.remove('is-invalid');
        this.classList.add('is-valid');
        emailError.style.display = 'none';
      }
    });
    
    
    passwordInput.addEventListener('input', function() {
      if (this.value.trim() === '') {
        this.classList.remove('is-valid');
        this.classList.add('is-invalid');
        passwordError.textContent = 'Password is required.';
        passwordError.style.display = 'block';
      } else if (this.value.length < 6) {
        this.classList.remove('is-valid');
        this.classList.add('is-invalid');
        passwordError.textContent = 'Password must be at least 6 characters long.';
        passwordError.style.display = 'block';
      } else {
        this.classList.remove('is-invalid');
        this.classList.add('is-valid');
        passwordError.style.display = 'none';
      }
    });
    
   
    form.addEventListener('submit', function(event) {
      let isValid = true;
      
      
      if (emailInput.value.trim() === '') {
        emailInput.classList.add('is-invalid');
        emailError.textContent = 'Email address is required.';
        emailError.style.display = 'block';
        isValid = false;
      } else if (!validateEmail(emailInput.value)) {
        emailInput.classList.add('is-invalid');
        emailError.textContent = 'Please enter a valid email address.';
        emailError.style.display = 'block';
        isValid = false;
      }
      
      
      if (passwordInput.value.trim() === '') {
        passwordInput.classList.add('is-invalid');
        passwordError.textContent = 'Password is required.';
        passwordError.style.display = 'block';
        isValid = false;
      } else if (passwordInput.value.length < 6) {
        passwordInput.classList.add('is-invalid');
        passwordError.textContent = 'Password must be at least 6 characters long.';
        passwordError.style.display = 'block';
        isValid = false;
      }
      
      if (!isValid) {
        event.preventDefault();
      }
    });
  });
</script>
</body>
</html>