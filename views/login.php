<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mydb";


$myconnection = mysqli_connect($servername, $username, $password, $dbname);
if (!$myconnection) {
    die("Connection failed: " . mysqli_connect_error());
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($myconnection, $sql);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);

    
        if ($row['password'] == $password) {
            $message = " Login successful! Welcome " . htmlspecialchars($row['name']);
        } else {
            $message = " Invalid password!";
        }
    } else {
        $message = " No user found with this email!";
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
  <style>
    body {
      background-color: #f8f9fa;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    form {
      background-color: white;
      width: 400px;
      height: auto;
      padding: 20px;
      margin-top: 100px;
    }
    h1 {
      text-align: center;
    }
  </style>
</head>
<body>

<form method="POST" action="">
  <h1>Cafeteria</h1>
  <div class="mb-3">
    <label for="email" class="form-label">Email:</label>
    <input type="email" class="form-control" id="email" name="email" required />
  </div>
  <div class="mb-3">
    <label for="password" class="form-label">Password:</label>
    <input type="password" class="form-control" id="password" name="password" required />
  </div>
  <div class="mb-3">
    <a href="forget-password.php">Forget password?</a>
  </div>
  <div class="mb-3">
    <button type="submit" class="btn btn-primary">Login</button>
  </div>

  <?php if (!empty($message)): ?>
    <div class="alert alert-info"><?php echo $message; ?></div>
  <?php endif; ?>
</form>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>