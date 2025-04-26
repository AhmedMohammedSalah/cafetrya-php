<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Checks</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    .navbar-custom {
        background: linear-gradient(90deg, #007bff, #00c6ff);
        padding: 10px 0;
        height: 70px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }
    .navbar-brand {
        font-size: 1.6rem;
        font-weight: 700;
        color: white;
        display: flex;
        align-items: center;
    }
    .navbar-brand i {
        font-size: 1.8rem;
        margin-right: 8px;
    }
    .nav-link {
        font-size: 1rem;
        color: white !important;
        margin: 0 10px;
        transition: color 0.3s, background-color 0.3s;
        border-radius: 5px;
    }
    .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.2);
        color: #fff !important;
    }
    .nav-link.active {
        background-color: rgba(255, 255, 255, 0.3);
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
    .navbar-toggler {
        border: none;
    }
    @media (max-width: 992px) {
        .nav-link {
            margin: 5px 0;
        }
        .user-info {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid rgba(255,255,255,0.2);
        }
    }
</style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container">
        <a class="navbar-brand" href="#">
            Coffee Admin
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="listProducts.php">Products</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="user.php">Users</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="checks.php">Checks</a>
                </li>
            </ul>
            <div class="d-flex align-items-center">
                <?php if (isset($_SESSION['user_name'])): ?>
                    <span class="user-name"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                <?php else: ?>
                    <span class="user-name">Guest</span>
                <?php endif; ?>
                <i class="fa-solid fa-user-tie"></i>
            </div>
        </div>
    </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
