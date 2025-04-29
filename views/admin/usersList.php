<?php

include_once(__DIR__ . '/../../models/user.php');
include_once(__DIR__ . '/../../models/room.php');


$itemsPerPage = 10;
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($currentPage - 1) * $itemsPerPage;


$allUsers = getAllUsers();
$totalUsers = mysqli_num_rows($allUsers);
$totalPages = ceil($totalUsers / $itemsPerPage);


$sql = "SELECT * FROM users ORDER BY id ASC LIMIT $itemsPerPage OFFSET $offset";
include(__DIR__ . '/../../connection.php');
$users = mysqli_query($myconnection, $sql);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete'])) {
        $userId = $_POST['user_id'];
        if (deleteUser($userId)) {
            $_SESSION['message'] = 'User deleted successfully.';
            $_SESSION['message_type'] = 'success';
            header("Location: usersList.php?page=$currentPage");
            exit();
        } else {
            $_SESSION['message'] = 'Failed to delete user.';
            $_SESSION['message_type'] = 'danger';
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #f8f9fc;
            --accent-color: #1cc88a;
            --danger-color: #e74a3b;
            --warning-color: #f6c23e;
            --sidebar-width: 250px;
        }
        
        body {
            background-color: #f8f9fc;
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, sans-serif;
            overflow-x: hidden;
        }
        

        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: linear-gradient(180deg, var(--primary-color) 0%, #224abe 100%);
            color: white;
            padding: 20px 0;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            z-index: 1000;
            transition: all 0.3s;
        }
        
        .sidebar-brand {
            height: 4.375rem;
            text-decoration: none;
            font-size: 1.2rem;
            font-weight: 800;
            padding: 1.5rem 1rem;
            text-align: center;
            letter-spacing: 0.05rem;
            color: white;
            display: block;
            margin-bottom: 1rem;
        }
        
        .sidebar-divider {
            border-top: 1px solid rgba(255, 255, 255, 0.15);
            margin: 1rem 0;
        }
        
        .sidebar-item {
            padding: 0.75rem 1rem;
            margin: 0 0.5rem;
            border-radius: 0.35rem;
            color: rgba(255, 255, 255, 0.8);
            transition: all 0.3s;
            display: block;
            text-decoration: none;
        }
        
        .sidebar-item:hover, .sidebar-item.active {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            text-decoration: none;
        }
        
        .sidebar-item i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
            transition: all 0.3s;
        }
        
        .page-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, #224abe 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 0.35rem;
            margin-bottom: 2rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        
        .page-header h1 {
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
        }
        
        .page-header h1 i {
            margin-right: 15px;
            font-size: 1.5rem;
        }
        
        .table-responsive {
            background-color: white;
            border-radius: 0.35rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            padding: 1.5rem;
        }
        
        .table th {
            background-color: var(--secondary-color);
            color: #5a5c69;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
        }
        
        .user-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 50%;
            border: 1px solid #e3e6f0;
        }
        
        .action-btn {
            padding: 0.35rem 0.75rem;
            margin: 0 0.25rem;
        }
        
        .add-user-btn {
            margin-bottom: 1.5rem;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 0;
                overflow: hidden;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .sidebar.active {
                width: var(--sidebar-width);
            }
            
            .main-content.active {
                margin-left: var(--sidebar-width);
            }
            
            .table-responsive {
                padding: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <a href="#" class="sidebar-brand d-flex align-items-center justify-content-center">
            <i class="fas fa-store me-2"></i>
            <span>Admin Panel</span>
        </a>
        
        <div class="sidebar-divider"></div>
        
        <div class="nav flex-column">
            <a href="home.php" class="sidebar-item">
                <i class="fas fa-home"></i>
                <span>Home</span>
            </a>
            
            <a href="listProducts.php" class="sidebar-item">
                <i class="fas fa-box-open"></i>
                <span>Products</span>
            </a>
            
            <a href="usersList.php" class="sidebar-item active">
                <i class="fas fa-users"></i>
                <span>Users</span>
            </a>
            
            <a href="checks.php" class="sidebar-item">
                <i class="fas fa-file-invoice-dollar"></i>
                <span>Checks</span>
            </a>
            
            <a href="unfinshedOrders.php" class="sidebar-item">
                <i class="fas fa-clipboard-list"></i>
                <span>Pending Orders</span>
            </a>
        </div>
    </div>
    
    <div class="main-content">

        <div class="container mt-4">
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-<?= $_SESSION['message_type'] ?> alert-dismissible fade show" role="alert">
                    <?= $_SESSION['message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
            <?php endif; ?>
            
            <div class="page-header">
                <h1><i class="fas fa-users"></i> User Management</h1>
            </div>
            
            <a href="addUser.php" class="btn btn-primary add-user-btn">
                <i class="fas fa-user-plus"></i> Add New User
            </a>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Age</th>
                            <th>Room</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while( $user=mysqli_fetch_assoc($users)): ?>
                            <tr>
                                <td><?= $user['id'] ?></td>
                                <td>
                                    <?php if (!empty($user['image'])): ?>
                                        <img src="<?= '../user/'.$user['image'] ?>" 
                                             alt="<?= $user['name'] ?>" 
                                             class="user-img img-thumbnail">
                                    <?php else: ?>
                                        <div class="user-img bg-light d-flex align-items-center justify-content-center">
                                            <i class="fas fa-user text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td><?= $user['name']?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td><?= $user['age'] ?? 'N/A' ?></td>
                                <td><?= $user['room_id']?></td>
                                <td>
                                    <a href="editUser.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-warning action-btn">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                        <button type="button" class="btn btn-sm btn-danger action-btn delete-btn" data-bs-toggle="modal" data-bs-target="#deleteConfirmModal" data-user-id="<?= $user['id'] ?>">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                
                <?php if ($totalPages > 1): ?>
                    <nav aria-label="Page navigation" class="mt-4">
                        <ul class="pagination justify-content-center">

                            <li class="page-item <?= $currentPage == 1 ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $currentPage - 1 ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                            
                       

                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            
                     

                            <li class="page-item <?= $currentPage == $totalPages ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $currentPage + 1 ?>" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                    
                    <div class="text-center text-muted">
                        Showing <?= ($offset + 1) ?> to <?= min($offset + $itemsPerPage, $totalUsers) ?> of <?= $totalUsers ?> users
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    

    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteConfirmModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this user? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        <input type="hidden" name="user_id" id="deleteUserId" value="">
                        <button type="submit" name="delete" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>

            document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');
            const toggleBtn = document.createElement('button');
            
            toggleBtn.innerHTML = '<i class="fas fa-bars"></i>';
            toggleBtn.className = 'btn btn-primary d-md-none position-fixed';
            toggleBtn.style.top = '10px';
            toggleBtn.style.left = '10px';
            toggleBtn.style.zIndex = '1001';
            
            toggleBtn.addEventListener('click', function() {
                sidebar.classList.toggle('active');
                mainContent.classList.toggle('active');
            });
            
            document.body.appendChild(toggleBtn);
            
            // Set up event listeners for all delete buttons
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const userId = this.getAttribute('data-user-id');
                    document.getElementById('deleteUserId').value = userId;
                });
            });
        });
    </script>
</body>
</html>