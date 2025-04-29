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
  
include_once "../../connection.php";

if(isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['new_status'];
    
    $update_query = "UPDATE orders SET status = '" . mysqli_real_escape_string($myconnection, $new_status) . "' 
                    WHERE id = " . mysqli_real_escape_string($myconnection, $order_id);
    
    if(mysqli_query($myconnection, $update_query)) {
        $status_message = "Order #" . $order_id . " status updated to " . $new_status;
        $status_type = "success";
    } else {
        $status_message = "Error updating order status: " . mysqli_error($myconnection);
        $status_type = "danger";
    }
}

$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';
$selected_user = isset($_GET['user']) ? $_GET['user'] : '';
$selected_status = isset($_GET['status']) ? $_GET['status'] : '';

$users_query = "SELECT DISTINCT id, name FROM users ORDER BY name";
$users_result = mysqli_query($myconnection, $users_query);

$orders_query = "SELECT o.id, o.user_id, u.name as username, o.total, o.status, o.created_at 
                FROM orders o
                JOIN users u ON o.user_id = u.id
                WHERE 1=1";

if (!empty($date_from)) {
    $orders_query .= " AND DATE(o.created_at) >= '" . mysqli_real_escape_string($myconnection, $date_from) . "'";
}
if (!empty($date_to)) {
    $orders_query .= " AND DATE(o.created_at) <= '" . mysqli_real_escape_string($myconnection, $date_to) . "'";
}
if (!empty($selected_user)) {
    $orders_query .= " AND o.user_id = " . mysqli_real_escape_string($myconnection, $selected_user);
}
if (!empty($selected_status)) {
    $orders_query .= " AND o.status = '" . mysqli_real_escape_string($myconnection, $selected_status) . "'";
}

$records_per_page = 5;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

$total_result = mysqli_query($myconnection, "SELECT COUNT(*) AS total FROM (" . $orders_query . ") as subquery");
$total_row = mysqli_fetch_assoc($total_result);
$total_pages = ceil($total_row['total'] / $records_per_page);

$orders_query .= " ORDER BY o.created_at DESC LIMIT $offset, $records_per_page";
$orders_result = mysqli_query($myconnection, $orders_query);

$category_counts = [];
$category_query = "SELECT c.name, COUNT(op.id) as count
                  FROM categories c
                  LEFT JOIN products p ON c.id = p.category_id
                  LEFT JOIN order_products op ON p.id = op.product_id
                  LEFT JOIN orders o ON op.order_id = o.id
                  WHERE 1=1";

if (!empty($date_from)) {
    $category_query .= " AND DATE(o.created_at) >= '" . mysqli_real_escape_string($myconnection, $date_from) . "'";
}
if (!empty($date_to)) {
    $category_query .= " AND DATE(o.created_at) <= '" . mysqli_real_escape_string($myconnection, $date_to) . "'";
}
if (!empty($selected_user)) {
    $category_query .= " AND o.user_id = " . mysqli_real_escape_string($myconnection, $selected_user);
}
if (!empty($selected_status)) {
    $category_query .= " AND o.status = '" . mysqli_real_escape_string($myconnection, $selected_status) . "'";
}

$category_query .= " GROUP BY c.name LIMIT 4";
$category_result = mysqli_query($myconnection, $category_query);

while ($row = ($category_result)) {
    $category_counts[$row['name']] = $row['count'];
}

function get_order_details($myconnection, $order_id) {
    $query = "SELECT op.id, op.quantity, op.price, op.notes, p.name as product_name, c.name as category_name, o.created_at
              FROM order_products op
              JOIN products p ON op.product_id = p.id
              JOIN categories c ON p.category_id = c.id
              JOIN orders o ON op.order_id = o.id
              WHERE op.order_id = " . mysqli_real_escape_string($myconnection, $order_id);
    
    return mysqli_query($myconnection, $query);
}

$status_query = "SELECT DISTINCT status FROM orders ORDER BY status";
$status_result = mysqli_query($myconnection, $status_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
         :root {
            --primary-color: #4e73df;
            --secondary-color: #f8f9fc;
            --accent-color: #1cc88a;
            --danger-color: #e74a3b;
            --text-color: #343a40;
            --text-color: #343a40;
            --title-color:rgb(76, 0, 255);
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
        

        .btn-coffee {
            background-color:  #4e73df;
            color: white;
        }
        .btn-coffee:hover {
            background-color:  #4e73df;
            color: white;
        }
        .page-item.active .page-link {
            background-color:  #4e73df;
            border-color:  #4e73df;
        }
        .page-link {
            color:  #4e73df;
        }
        .product-card {
            transition: all 0.3s;
            cursor: pointer;
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .product-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 4px;
        }
        h1, h2, h3 {
            color:  #4e73df;
        }
        .pagination {
            justify-content: center;
            margin-top: 20px;
        }
        .filter-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .summary-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: center;
            margin-bottom: 30px;
        }
        .summary-box {
            background-color: #fff;
            border-radius: 10px;
            padding: 20px;
            min-width: 160px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }
        .summary-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.15);
        }
        .summary-box h3 {
            font-size: 1.2rem;
            margin-bottom: 10px;
            color: #5a3c2a;
        }
        .summary-box p {
            font-size: 2rem;
            font-weight: bold;
            margin: 0;
            color: #6F4E37;
        }
        .expandable-row {
            cursor: pointer;
        }
        .expandable-row td:first-child:before {
            content: "+";
            margin-right: 8px;
            font-weight: bold;
            color:  #4e73df;
        }
        .expandable-row.expanded td:first-child:before {
            content: "-";
        }
        .detail-row {
            background-color: #f9f9f9;
        }
        .detail-row td {
            padding: 0;
        }
        .detail-content {
            padding: 15px;
        }
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
            display: inline-block;
        }
        .status-completed {
            background-color: #d1e7dd;
            color: #0f5132;
        }
        .status-cancelled {
            background-color: #f8d7da;
            color: #842029;
        }
        .status-out-for-delivery {
            background-color: #fff3cd;
            color:  #4e73df;
        }
        .status-default {
            background-color: #e2e3e5;
            color: #41464b;
        }
        .action-buttons form {
            display: inline-block;
        }
    </style>
</head>
<body>
<div class="d-flex">
<div class="sidebar bg-light p-3" style="width: 250px; min-height: 100vh;">
    <a href="#" class="sidebar-brand d-flex align-items-center justify-content-center mb-4">
      <i class="fas fa-store me-2"></i>
      <span>Admin Panel</span>
    </a>
    <div class="sidebar-divider"></div>
    <form method="POST">  
      <button type="submit" class="bg-light" style="border:none;" name="logout">
    <a class=" text-danger sidebar-item">Log Out</a>
                  </button> </form>
    <div class="nav flex-column">
      <a href="listProducts.php" class="sidebar-item mb-2">
        <i class="fas fa-box-open"></i> <span>Products</span>
      </a>
      <a href="usersList.php" class="sidebar-item mb-2">
        <i class="fas fa-users"></i> <span>Users</span>
      </a>
      <a href="checks.php" class="sidebar-item mb-2">
        <i class="fas fa-file-invoice-dollar"></i> <span>Checks</span>
      </a>
      <a href="unfinshedOrders.php" class="sidebar-item mb-2">
        <i class="fas fa-clipboard-list"></i> <span>Pending Orders</span>
      </a>
      <a href="addOrder.php" class="sidebar-item mb-2">
        <i class="fa-solid fa-cart-shopping"></i> <span>Manual Orders</span>
      </a>
    </div>
  </div>
    <div class="container mt-4">
        <?php if(isset($status_message)): ?>
        <div class="alert alert-<?php echo $status_type; ?> alert-dismissible fade show" role="alert">
            <?php echo $status_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Orders</h1>
        </div>
        <div class="filter-section">
            <form method="GET" action="" class="row g-3">
                <div class="col-md-3">
                    <label for="date_from" class="form-label">Date from:</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" value="<?php echo $date_from; ?>">
                </div>
                <div class="col-md-3">
                    <label for="date_to" class="form-label">Date to:</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" value="<?php echo $date_to; ?>">
                </div>
                <div class="col-md-3">
                    <label for="user" class="form-label">User:</label>
                    <select class="form-select" id="user" name="user">
                        <option value="">All Users</option>
                        <?php while ($user = mysqli_fetch_assoc($users_result)): ?>
                        <option value="<?php echo $user['id']; ?>" <?php if($selected_user == $user['id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($user['name']); ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Status:</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Statuses</option>
                        <?php while ($status = mysqli_fetch_assoc($status_result)): ?>
                        <option value="<?php echo $status['status']; ?>" <?php if($selected_status == $status['status']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($status['status']); ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-coffee">
                      Apply Filters
                    </button>
                </div>
            </form>
        </div>
        <div class="summary-container">
            <?php
            if (count($category_counts) > 0) {
                foreach ($category_counts as $category => $count) {
                    echo "<div class='summary-box'>";
                    echo "<h3>" . htmlspecialchars($category) . "</h3>";
                    echo "<p>" . htmlspecialchars($count) . "</p>";
                    echo "</div>";
                }
            } else {
               
            }
            ?>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Order Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(mysqli_num_rows($orders_result) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($orders_result)): ?>
                                    <tr class="expandable-row" id="row-<?php echo $row['id']; ?>" 
                                        onclick="toggleDetails(<?php echo $row['id']; ?>)">
                                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                                        <td><?php echo htmlspecialchars($row['total']); ?> EGP</td>
                                        <td>
                                            <?php 
                                            $status_class = '';
                                            switch($row['status']) {
                                                
                                                case 'cancelled':
                                                    $status_class = 'status-cancelled';
                                                    break;
                                               
                                                default:
                                                    $status_class = 'status-default';
                                            }
                                            ?>
                                            <span class="status-badge <?php echo $status_class; ?>">
                                                <?php echo ucfirst(htmlspecialchars($row['status'])); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('Y/m/d h:i A', strtotime($row['created_at'])); ?></td>
                                        <td class="action-buttons" onclick="event.stopPropagation();">
                                            
                                            
                                            <?php if($row['status'] != 'cancelled'): ?>
                                            <form method="POST" action="">
                                                <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                                <input type="hidden" name="new_status" value="cancelled">
                                                <button type="submit" name="update_status" class="btn btn-sm btn-danger ms-1">
                                                   Cancel
                                                </button>
                                            </form>
                                            <?php endif; ?>
                                            
                                            
                                            
                                               
                                        </td>
                                    </tr>
                                    <tr class="detail-row" id="details-<?php echo $row['id']; ?>" style="display: none;">
                                        <td colspan="5">
                                            <div class="detail-content">
                                                <?php
                                                $details = get_order_details($myconnection, $row['id']);
                                                if(mysqli_num_rows($details) > 0):
                                                ?>
                                                    <table class="table table-sm">
                                                        <thead>
                                                            <tr>
                                                                <th>Product</th>
                                                                <th>Category</th>
                                                                <th>Quantity</th>
                                                                <th>Price</th>
                                                                <th>Total</th>
                                                                <th>Notes</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php while($detail = mysqli_fetch_assoc($details)): ?>
                                                                <tr>
                                                                    <td><?php echo htmlspecialchars($detail['product_name']); ?></td>
                                                                    <td><?php echo htmlspecialchars($detail['category_name']); ?></td>
                                                                    <td><?php echo htmlspecialchars($detail['quantity']); ?></td>
                                                                    <td><?php echo htmlspecialchars($detail['price']); ?> EGP</td>
                                                                    <td><?php echo htmlspecialchars($detail['price'] * $detail['quantity']); ?> EGP</td>
                                                                    <td><?php echo htmlspecialchars($detail['notes']); ?></td>
                                                                </tr>
                                                            <?php endwhile; ?>
                                                        </tbody>
                                                    </table>
                                                <?php else: ?>
                                                    <p class="text-muted">No details available for this order.</p>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4">No orders found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php if($total_pages > 1): ?>
                <nav aria-label="Page navigation">
                    <ul class="pagination">
                        <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=1&date_from=<?php echo $date_from; ?>&date_to=<?php echo $date_to; ?>&user=<?php echo $selected_user; ?>&status=<?php echo $selected_status; ?>" aria-label="First">
                                <span aria-hidden="true">&laquo;&laquo;</span>
                            </a>
                        </li>
                        <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page-1; ?>&date_from=<?php echo $date_from; ?>&date_to=<?php echo $date_to; ?>&user=<?php echo $selected_user; ?>&status=<?php echo $selected_status; ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                        
                        <?php for($i = max(1, $page-2); $i <= min($page+2, $total_pages); $i++): ?>
                            <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>&date_from=<?php echo $date_from; ?>&date_to=<?php echo $date_to; ?>&user=<?php echo $selected_user; ?>&status=<?php echo $selected_status; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page+1; ?>&date_from=<?php echo $date_from; ?>&date_to=<?php echo $date_to; ?>&user=<?php echo $selected_user; ?>&status=<?php echo $selected_status; ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                        <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $total_pages; ?>&date_from=<?php echo $date_from; ?>&date_to=<?php echo $date_to; ?>&user=<?php echo $selected_user; ?>&status=<?php echo $selected_status; ?>" aria-label="Last">
                                <span aria-hidden="true">&raquo;&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
        </div>
        </div>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleDetails(id) {
            const row = document.getElementById('row-' + id);
            const details = document.getElementById('details-' + id);
            
            if (details.style.display === 'none' || details.style.display === '') {
                details.style.display = 'table-row';
                row.classList.add('expanded');
            } else {
                details.style.display = 'none';
                row.classList.remove('expanded');
            }
        }
        
        window.addEventListener('DOMContentLoaded', (event) => {
            setTimeout(function() {
                const alertElements = document.querySelectorAll('.alert');
                alertElements.forEach(function(alert) {
                    const closeButton = alert.querySelector('.btn-close');
                    if (closeButton) {
                        closeButton.click();
                    }
                });
            }, 3000);
        });
    </script>
</body>
</html>

<?php
mysqli_close($myconnection);
?>