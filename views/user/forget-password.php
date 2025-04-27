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

  <form method="POST" action="" id="resetForm">
    <div class="form-group mb-4">
      <i class="fa-solid fa-envelope"></i>
      <input type="email" class="form-control" id="email" name="email" placeholder="Email address" required />
      <div class="error-message" id="emailError">Please enter a valid email address.</div>
    </div>
    <div class="form-group mb-4">
      <i class="fa-solid fa-lock"></i>
      <input type="password" class="form-control" id="new_password" name="new_password" placeholder="New password" required />
      <div class="error-message" id="newPasswordError">Password must be at least 6 characters long.</div>
    </div>
    <div class="form-group mb-4">
      <i class="fa-solid fa-lock"></i>
      <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm new password" required />
      <div class="error-message" id="confirmPasswordError">Passwords do not match.</div>
    </div>
    <button type="submit" class="btn btn-primary btn-reset">Update Password</button>
  </form>

  <a href="index.php" class="back-link">Back to Login</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('resetForm');
  const emailInput = document.getElementById('email');
  const newPasswordInput = document.getElementById('new_password');
  const confirmPasswordInput = document.getElementById('confirm_password');

  const emailError = document.getElementById('emailError');
  const newPasswordError = document.getElementById('newPasswordError');
  const confirmPasswordError = document.getElementById('confirmPasswordError');

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

  newPasswordInput.addEventListener('input', function() {
    if (this.value.trim() === '') {
      this.classList.remove('is-valid');
      this.classList.add('is-invalid');
      newPasswordError.textContent = 'Password is required.';
      newPasswordError.style.display = 'block';
    } else if (this.value.length < 6) {
      this.classList.remove('is-valid');
      this.classList.add('is-invalid');
      newPasswordError.textContent = 'Password must be at least 6 characters long.';
      newPasswordError.style.display = 'block';
    } else {
      this.classList.remove('is-invalid');
      this.classList.add('is-valid');
      newPasswordError.style.display = 'none';
    }
  });

  confirmPasswordInput.addEventListener('input', function() {
    if (this.value.trim() === '') {
      this.classList.remove('is-valid');
      this.classList.add('is-invalid');
      confirmPasswordError.textContent = 'Please confirm your password.';
      confirmPasswordError.style.display = 'block';
    } else if (this.value !== newPasswordInput.value) {
      this.classList.remove('is-valid');
      this.classList.add('is-invalid');
      confirmPasswordError.textContent = 'Passwords do not match.';
      confirmPasswordError.style.display = 'block';
    } else {
      this.classList.remove('is-invalid');
      this.classList.add('is-valid');
      confirmPasswordError.style.display = 'none';
    }
  });

  form.addEventListener('submit', function(event) {
    let isValid = true;

    if (emailInput.value.trim() === '' || !validateEmail(emailInput.value)) {
      emailInput.classList.add('is-invalid');
      emailError.style.display = 'block';
      isValid = false;
    }
    if (newPasswordInput.value.trim() === '' || newPasswordInput.value.length < 6) {
      newPasswordInput.classList.add('is-invalid');
      newPasswordError.style.display = 'block';
      isValid = false;
    }
    if (confirmPasswordInput.value.trim() === '' || confirmPasswordInput.value !== newPasswordInput.value) {
      confirmPasswordInput.classList.add('is-invalid');
      confirmPasswordError.style.display = 'block';
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