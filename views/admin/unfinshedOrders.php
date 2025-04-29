<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location:'/../../user/login.php");
    exit; 
  }
  
  if (isset($_POST['logout'])) {    
    session_destroy();
    header("Location:'/../../user/login.php");
  }
  
// Pagination settings
include_once(__DIR__ . '/../../models/order.php');
$itemsPerPage = 10; // Number of orders per page
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($currentPage - 1) * $itemsPerPage;
$totalOrders = getPendingOrdersCount();
$orders = getPendingOrdersPaginated($itemsPerPage, $offset);
$totalPages = ceil($totalOrders / $itemsPerPage);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Orders Dashboard</title>
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
        
        /* Sidebar Styles */
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
        
        .container {
            max-width: 1200px;
            margin-top: 2rem;
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
        
        .accordion-item {
            border: none;
            border-radius: 0.35rem;
            overflow: hidden;
            margin-bottom: 1rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            transition: all 0.3s ease;
        }
        
        .accordion-item:hover {
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.25);
        }
        
        .accordion-button {
            background-color: white;
            padding: 1.5rem;
            font-weight: 600;
            color: #5a5c69;
        }
        
        .accordion-button:not(.collapsed) {
            background-color: var(--secondary-color);
            color: var(--primary-color);
            box-shadow: none;
        }
        
        .accordion-button:focus {
            box-shadow: none;
            border-color: rgba(0,0,0,.125);
        }
        
        .accordion-body {
            padding: 2rem;
            background-color: var(--secondary-color);
        }
        
        .order-header {
            display: flex;
            justify-content: space-between;
            width: 100%;
            align-items: center;
        }
        
        .order-id {
            font-weight: bold;
            color: var(--primary-color);
        }
        
        .order-date {
            color: #858796;
            font-size: 0.9rem;
        }
        
        .customer {
            color: #5a5c69;
        }
        
        .order-total {
            font-weight: bold;
            color: var(--primary-color);
            font-size: 1.1rem;
        }
        
        .product-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 0.35rem;
            border: 1px solid #e3e6f0;
        }
        
        .badge {
            padding: 0.5em 0.75em;
            font-weight: 600;
            letter-spacing: 0.05em;
        }
        
        .bg-warning {
            background-color: var(--warning-color) !important;
        }
        
        .bg-success {
            background-color: var(--accent-color) !important;
        }
        
        .btn-success {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
        }
        
        .btn-danger {
            background-color: var(--danger-color);
            border-color: var(--danger-color);
        }
        
        .btn {
            padding: 0.5rem 1.5rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            transition: all 0.2s;
        }
        
        .btn i {
            margin-right: 8px;
        }
        
        .info-card {
            background: white;
            border-radius: 0.35rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
        }
        
        .info-card h5 {
            color: var(--primary-color);
            border-bottom: 1px solid #e3e6f0;
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
            font-weight: 600;
        }
        
        .info-card p {
            margin-bottom: 0.5rem;
        }
        
        .table {
            background-color: white;
            border-radius: 0.35rem;
            overflow: hidden;
        }
        
        .table th {
            background-color: var(--secondary-color);
            color: #5a5c69;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
        }
        
        .notes-highlight {
            background-color: rgba(30, 200, 138, 0.1);
            border-left: 4px solid var(--accent-color);
            padding: 1rem;
            border-radius: 0 0.35rem 0.35rem 0;
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
            
            .order-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }
            
            .accordion-button {
                padding: 1rem;
            }
            
            .accordion-body {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar Navigation -->
     <div class="sidebar">
            <a href="#" class="sidebar-brand d-flex align-items-center justify-content-center">
                <i class="fas fa-store me-2"></i>
                <span>Admin Panel</span>
            </a>
            
            <div class="sidebar-divider"></div>
            
            <div class="nav flex-column">
            <form method="POST">  
      <button type="submit" class="bg-light" style="border:none;" name="logout">
    <a class=" text-danger sidebar-item">Log Out</a>
                  </button> </form>
                <a href="listProducts.php" class="sidebar-item">
                    <i class="fas fa-box-open"></i>
                    <span>Products</span>
                </a>
                
                <a href="usersList.php" class="sidebar-item">
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
                <a href="addOrder.php" class="sidebar-item mb-2">
        <i class="fa-solid fa-cart-shopping"></i> <span>Manual Orders</span>
      </a>
            </div>
        </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <div class="container mt-4">
            <div class="page-header">
                <h1><i class="fas fa-clipboard-list"></i> Pending Orders Management</h1>
            </div>
            
            <?php
            // Include necessary files
            include_once(__DIR__ . '/../../connection.php');
            include_once(__DIR__ . '/../../models/product.php');
            include_once(__DIR__ . '/../../models/order.php');
            
            // Handle form submission for order status updates
            if (isset($_POST['completed']) || isset($_POST['cancelled'])) {
                $orderId = $_POST['order_id'] ?? 0;
                $status = isset($_POST['completed']) ? 'delivered' : 'cancelled';
                if (updateOrderStatus($orderId, $status)) {
                    echo '<div class="alert alert-success">Order status updated successfully.</div>';
                    echo '<script>setTimeout(function(){ window.location.href = "unfinshedOrders.php"; }, 2000);</script>';
                    exit();
                } else {
                    echo '<div class="alert alert-danger">Failed to update order status.</div>';
                }
            }
            
            if (empty($orders)): ?>
                <div class="alert alert-info d-flex align-items-center">
                    <i class="fas fa-info-circle me-2"></i>
                    No pending orders found.
                </div>
            <?php else: ?>
                <div class="accordion" id="ordersAccordion">
                    <?php foreach ($orders as $order): 
                        $orderItems = getOrderItems($order['id'] ?? 0);
                        $orderTotal = 0;
                        // Fetch customer information
                        include_once(__DIR__ . '/../../models/user.php');
                        $customer = getUserById($order['user_id'] ?? 1);
                        ?>
                        <div class="accordion-item mb-3">
                            <h2 class="accordion-header" id="heading<?= $order['id'] ?? '' ?>">
                                <button class="accordion-button collapsed" type="button" 
                                        data-bs-toggle="collapse" data-bs-target="#collapse<?= $order['id'] ?? '' ?>" 
                                        aria-expanded="false" aria-controls="collapse<?= $order['id'] ?? '' ?>">
                                    <div class="order-header">
                                        <span class="order-id"><i class="fas fa-hashtag me-1"></i> #<?= $order['id'] ?? 'N/A' ?></span>
                                        <span class="order-date"><i class="far fa-calendar-alt me-1"></i> <?= isset($order['created_at']) ? date('M d, Y H:i', strtotime($order['created_at'])) : 'Date N/A' ?></span>
                                        <span class="customer"><i class="fas fa-user me-1"></i> <?= htmlspecialchars($customer['name'] ?? 'Customer N/A') ?></span>
                                        <span class="order-total"><i class="fas fa-receipt me-1"></i> $<?= isset($order['total']) ? number_format($order['total'], 2) : '0.00' ?></span>
                                    </div>
                                </button>
                            </h2>
                            <div id="collapse<?= $order['id'] ?? '' ?>" class="accordion-collapse collapse" 
                                 aria-labelledby="heading<?= $order['id'] ?? '' ?>" data-bs-parent="#ordersAccordion">
                                <div class="accordion-body">
                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <div class="info-card">
                                                <h5><i class="fas fa-user-tag me-2"></i>Customer Information</h5>
                                                <p><i class="fas fa-user me-2 text-muted"></i> <strong>Name:</strong> <?= htmlspecialchars($customer['name'] ?? 'N/A') ?></p>
                                                <p><i class="fas fa-envelope me-2 text-muted"></i> <strong>Email:</strong> <?= htmlspecialchars($customer['email'] ?? 'N/A') ?></p>
                                                <?php if(isset($customer['phone']) && !empty($customer['phone'])): ?>
                                                    <p><i class="fas fa-phone me-2 text-muted"></i> <strong>Phone:</strong> <?= htmlspecialchars($customer['phone']) ?></p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-card">
                                                <h5><i class="fas fa-clipboard-check me-2"></i>Order Information</h5>
                                                <p><i class="fas fa-tag me-2 text-muted"></i> <strong>Status:</strong> 
                                                    <span class="badge bg-warning text-dark"><?= isset($order['status']) ? ucfirst($order['status']) : 'N/A' ?></span>
                                                </p>
                                                <p><i class="far fa-clock me-2 text-muted"></i> <strong>Date:</strong> <?= isset($order['created_at']) ? date('F j, Y g:i a', strtotime($order['created_at'])) : 'N/A' ?></p>
                                                <?php if(isset($order['notes']) && !empty($order['notes'])): ?>
                                                    <div class="notes-highlight mt-3">
                                                        <p class="mb-1"><strong><i class="fas fa-sticky-note me-2"></i>Notes:</strong></p>
                                                        <p class="mb-0"><?= htmlspecialchars($order['notes']) ?></p>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <?php if (!empty($orderItems)): ?>
                                    <h5 class="mt-4 mb-3"><i class="fas fa-box-open me-2"></i>Order Items</h5>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Product</th>
                                                    <th>Image</th>
                                                    <th>Price</th>
                                                    <th>Quantity</th>
                                                    <th>Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($orderItems as $item): 
                                                    $product = getProductById($item['product_id'] ?? 0);
                                                    $subtotal = ($item['quantity'] ?? 1) * ($product['price'] ?? 0);
                                                    $orderTotal += $subtotal;
                                                ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($product['product_name'] ?? 'Product N/A') ?></td>
                                                        <td>
                                                            <?php if (!empty($product['image'])): ?>
                                                                <img src="<?= $product['image'] ?>" 
                                                                     alt="<?= $product['product_name'] ?? '' ?>" 
                                                                     class="product-img img-thumbnail">
                                                            <?php else: ?>
                                                                <div class="product-img bg-light d-flex align-items-center justify-content-center">
                                                                    <i class="fas fa-image text-muted"></i>
                                                                </div>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>$<?= isset($product['price']) ? number_format($product['price'], 2) : '0.00' ?></td>
                                                        <td><?= $item['quantity'] ?? 0 ?></td>
                                                        <td>$<?= number_format($subtotal, 2) ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                                <tr class="table-active">
                                                    <td colspan="4" class="text-end fw-bold"><i class="fas fa-calculator me-2"></i>Total:</td>
                                                    <td class="fw-bold">$<?= number_format($orderTotal, 2) ?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php else: ?>
                                        <div class="alert alert-warning d-flex align-items-center">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            No items found for this order.
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="d-flex justify-content-end mt-4 gap-3">
                                        <form method="POST">
                                            <input type="hidden" name="order_id" value="<?= $order['id'] ?? '' ?>">
                                            <button type="submit" name="completed" class="btn btn-success">
                                                <i class="fas fa-check-circle"></i> Mark as Completed
                                            </button>
                                        </form>
                                        <form method="POST">
                                            <input type="hidden" name="order_id" value="<?= $order['id'] ?? '' ?>">
                                            <button type="submit" name="cancelled" class="btn btn-danger">
                                                <i class="fas fa-times-circle"></i> Cancel Order
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($totalPages > 1): ?>
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <!-- Previous Page Link -->
                        <li class="page-item <?= $currentPage == 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $currentPage - 1 ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                        
                        <!-- Page Numbers -->
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <!-- Next Page Link -->
                        <li class="page-item <?= $currentPage == $totalPages ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $currentPage + 1 ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
                
                <div class="text-center text-muted mb-4">
                    Showing <?= ($offset + 1) ?> to <?= min($offset + $itemsPerPage, $totalOrders) ?> of <?= $totalOrders ?> pending orders
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add animation to accordion items
        document.querySelectorAll('.accordion-button').forEach(button => {
            button.addEventListener('click', () => {
                const icon = button.querySelector('.fas');
                if (icon) {
                    icon.classList.toggle('fa-chevron-down');
                    icon.classList.toggle('fa-chevron-up');
                }
            });
        });
        
        // Mobile sidebar toggle
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
        });
    </script>
</body>
</html>