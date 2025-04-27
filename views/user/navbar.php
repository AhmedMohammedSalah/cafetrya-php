<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cafeteria Order System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body {
      background-color: #f4f6f9;
    }

    .navbar-custom {
      background: linear-gradient(90deg, rgb(113, 80, 36);, rgb(110, 75, 30););
      padding: 10px 0;
      height: 70px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .navbar-brand {
      font-size: 1.7rem;
      font-weight: bold;
      color: white;
      display: flex;
      align-items: center;
    }

    .nav-link {
      font-size: 1rem;
      color: white !important;
      margin: 0 10px;
      border-radius: 8px;
      transition: all 0.3s ease-in-out;
    }

    .nav-link:hover {
      background-color: rgba(255, 255, 255, 0.2);
      color: white !important;
    }

    .nav-link.active {
      background-color: rgba(255, 255, 255, 0.4);
      font-weight: bold;
    }

    .user-avatar {
      width: 45px;
      height: 45px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid white;
      margin-left: 10px;
    }

    .user-name {
      color: white;
      font-size: 1rem;
      margin-right: 10px;
    }

    .product-card {
      background: white;
      border: none;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
      transition: all 0.3s ease-in-out;
    }

    .product-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }

    .order-section {
      background: #ffffff;
      padding: 20px;
      height: 100vh;
      border-left: 1px solid #dee2e6;
    }

    .order-item {
      border-bottom: 1px solid #eee;
      padding: 12px 0;
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .order-item-img {
      width: 50px;
      height: 50px;
      object-fit: cover;
      border-radius: 8px;
    }

    .btn-coffee {
      background: rgb(113, 80, 36);
      color: white;
      border-radius: 8px;
      padding: 8px 20px;
      font-weight: bold;
    }

    .btn-coffee:hover {
      background:rgb(113, 80, 36);
      color: white;
    }

    .page-item.active .page-link {
      background-color: rgb(113, 80, 36);
      border-color:rgb(113, 80, 36);
      border-radius: 8px;
    }

    .page-link {
      color:rgb(113, 80, 36);
    }

    .search-box {
      transition: all 0.3s ease;
      border-radius: 8px;
    }

    .search-box:focus-within {
      box-shadow: 0 0 0 0.25rem rgba(0, 131, 176, 0.25);
    }

    #search-input {
      border-color: rgb(113, 80, 36);
    }

    #search-input:focus {
      border-color:rgb(113, 80, 36);
      box-shadow: 0 0 0 0.25rem rgba(0, 131, 176, 0.25);
    }

    .alert-warning {
      background-color: #fff8e1;
      color: #795548;
      border-left: 5px solid #ffc107;
      border-radius: 8px;
    }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
  <div class="container">
    <a class="navbar-brand" href="#">
      Café Delight
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="nav-link" href="home.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="myorder.php">My Orders</a>
        </li>
      </ul>
      
      <div class="d-flex align-items-center">
        <!-- <span class="user-name"><?= $_SESSION['user_name'] ?></span> -->
        <i class="fa-solid fa-user text-light"></i>
      </div>
     
    </div>
  </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
