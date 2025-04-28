<?php
session_start();
include_once '../../connection.php';

$user_id = $_SESSION['user_id'];

if (!$myconnection) {
  die("Connection failed: " . mysqli_connect_error());
}

if (isset($_GET['cancel_id'])) {
  $cancel_id = mysqli_real_escape_string($myconnection, $_GET['cancel_id']);
  $query = "UPDATE orders SET status='cancelled' WHERE id='$cancel_id' AND user_id='$user_id' AND status='pending'";
  $result = mysqli_query($myconnection, $query);
  if ($result) {
    $success_message = "Order successfully cancelled.";
  } else {
    $error_message = "Error cancelling order: " . mysqli_error($myconnection);
  }
  header("Location: myorder.php");
  exit;
}


$from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$to = isset($_GET['date_to']) ? $_GET['date_to'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

$where = "user_id = '$user_id'";

if ($from) {
  $from = mysqli_real_escape_string($myconnection, $from);
  $where .= " AND created_at >= '$from 00:00:00'";
}
if ($to) {
  $to = mysqli_real_escape_string($myconnection, $to);
  $where .= " AND created_at <= '$to 23:59:59'";
}
if ($status_filter) {
  $status_filter = mysqli_real_escape_string($myconnection, $status_filter);
  $where .= " AND status = '$status_filter'";
}


$user_query = "SELECT name FROM users WHERE id = '$user_id'";
$user_result = mysqli_query($myconnection, $user_query);
$user_name = "";
if ($user_result && mysqli_num_rows($user_result) > 0) {
  $user_data = mysqli_fetch_assoc($user_result);
  $user_name = $user_data['name'];
}


$sql = "
  SELECT id, created_at AS order_date, status, total, notes, room_id
  FROM orders
  WHERE $where
  ORDER BY created_at DESC
";

$result = mysqli_query($myconnection, $sql);
if (!$result) {
  die("Query failed: " . mysqli_error($myconnection));
}

$orders = [];
while ($row = mysqli_fetch_assoc($result)) {
  $orders[] = $row;
}


$items_per_page = 5;
$total_items = count($orders);
$total_pages = ceil($total_items / $items_per_page);
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($current_page - 1) * $items_per_page;

$current_page_orders = array_slice($orders, $offset, $items_per_page);


$rooms = [];
$room_query = "SELECT id, room_name FROM rooms";
$room_result = mysqli_query($myconnection, $room_query);
if ($room_result) {
  while ($room = mysqli_fetch_assoc($room_result)) {
    $rooms[$room['id']] = $room['room_name'];
  }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Order History | Coffee Shop</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root {
      --primary: #6F4E37;
      --secondary: #C4A484;
      --light-brown: #e6d7c3;
      --dark-brown: #5a3c2a;
    }
    
    body {
      background-color: #f8f9fa;
      color: #333;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    .header {
      background-color: var(--primary);
      color: white;
      padding: 20px 0;
      border-bottom: 5px solid var(--secondary);
    }
    
    .content-wrapper {
      max-width: 1200px;
      margin: 0 auto;
      padding: 30px 15px;
    }
    
    .user-info {
      display: flex;
      align-items: center;
      margin-bottom: 20px;
    }
    
    .user-name {
      font-size: 18px;
      font-weight: 500;
      margin-left: 10px;
    }
    
    .page-title {
      font-size: 28px;
      font-weight: 600;
      color: var(--dark-brown);
      margin-bottom: 25px;
      border-bottom: 2px solid var(--secondary);
      padding-bottom: 10px;
    }
    
    .filter-card {
      background: white;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
      padding: 20px;
      margin-bottom: 30px;
    }
    
    .orders-container {
      background: white;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
      overflow: hidden;
    }
    
    .table thead th {
      background-color: var(--primary);
      color: white;
      font-weight: 500;
      border: none;
    }
    
    .table tbody tr {
      transition: all 0.3s;
    }
    
    .table tbody tr:hover {
      background-color: rgba(196, 164, 132, 0.1);
    }
    
    .btn-primary {
      background-color: var(--primary);
      border-color: var(--primary);
    }
    
    .btn-primary:hover {
      background-color: var(--dark-brown);
      border-color: var(--dark-brown);
    }
    
    .btn-outline-primary {
      color: var(--primary);
      border-color: var(--primary);
    }
    
    .btn-outline-primary:hover {
      background-color: var(--primary);
      border-color: var(--primary);
    }
    
    .cancel-btn {
      background-color: #dc3545;
      color: white;
      border: none;
      padding: 5px 15px;
      border-radius: 4px;
      font-weight: 500;
      transition: all 0.3s;
    }
    
    .cancel-btn:hover {
      background-color: #c82333;
    }
    
    .status-badge {
      padding: 5px 10px;
      border-radius: 20px;
      font-size: 14px;
      font-weight: 500;
      display: inline-block;
    }
    
    .status-pending {
      background-color: #ffc107;
      color: #333;
    }
    
    .status-preparing {
      background-color: #17a2b8;
      color: white;
    }
    
    .status-delivered {
      background-color: #28a745;
      color: white;
    }
    
    .status-cancelled {
      background-color: #dc3545;
      color: white;
    }
    
    .order-details {
      background-color: #f9f9f9;
      border-radius: 8px;
      margin: 10px 0;
    }
    
    .card-header {
      background-color: #e9ecef;
      font-weight: 500;
    }
    
    .product-img {
      width: 40px;
      height: 40px;
      object-fit: cover;
      border-radius: 4px;
    }
    
    .pagination {
      justify-content: center;
      margin-top: 30px;
    }
    
    .page-item.active .page-link {
      background-color: var(--primary);
      border-color: var(--primary);
    }
    
    .page-link {
      color: var(--primary);
    }
    
    .footer {
      background-color: #343a40;
      color: white;
      padding: 20px 0;
      margin-top: 50px;
      text-align: center;
    }
    
    .back-link {
      display: inline-flex;
      align-items: center;
      margin-bottom: 20px;
      color: var(--primary);
      text-decoration: none;
      font-weight: 500;
    }
    
    .back-link:hover {
      color: var(--dark-brown);
    }
    
    
    .modal-header {
      background-color: var(--primary);
      color: white;
    }
    
    .modal-confirm .modal-content {
      border-radius: 10px;
    }
    
    .modal-confirm .btn-danger {
      background-color: #dc3545;
      border-color: #dc3545;
    }
    
    .modal-confirm .btn-secondary {
      background-color: #6c757d;
      border-color: #6c757d;
    }
    
    @media (max-width: 768px) {
      .filter-form .btn {
        width: 100%;
        margin-top: 10px;
      }
    }
  </style>
</head>
<body>
 

  <div class="content-wrapper">
    <a href="home.php" class="back-link">
      <i class="fas fa-arrow-left me-2"></i> Back to Home
    </a>
    
    <h2 class="page-title">
      <i class="fas fa-clipboard-list me-2"></i>Order History
    </h2>
    
    <?php if(isset($success_message)): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i><?= $success_message ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>
    
    <?php if(isset($error_message)): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i><?= $error_message ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>
    
    <div class="filter-card">
      <form class="row g-3 filter-form" method="get">
        <div class="col-md-3 col-sm-6">
          <label for="date_from" class="form-label">From Date</label>
          <input type="date" id="date_from" name="date_from" class="form-control" value="<?= htmlspecialchars($from) ?>">
        </div>
        <div class="col-md-3 col-sm-6">
          <label for="date_to" class="form-label">To Date</label>
          <input type="date" id="date_to" name="date_to" class="form-control" value="<?= htmlspecialchars($to) ?>">
        </div>
        <div class="col-md-3 col-sm-6">
          <label for="status" class="form-label">Status</label>
          <select name="status" id="status" class="form-select">
            <option value="">All Statuses</option>
            <option value="pending" <?= $status_filter === 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="preparing" <?= $status_filter === 'preparing' ? 'selected' : '' ?>>Preparing</option>
            <option value="delivered" <?= $status_filter === 'delivered' ? 'selected' : '' ?>>Delivered</option>
            <option value="cancelled" <?= $status_filter === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
          </select>
        </div>
        <div class="col-md-3 col-sm-6 d-flex align-items-end">
          <div class="d-grid gap-2 w-100">
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-filter me-2"></i>Apply Filters
            </button>
            <a href="myorder.php" class="btn btn-outline-primary">
              <i class="fas fa-redo me-2"></i>Reset
            </a>
          </div>
        </div>
      </form>
    </div>

    <?php if (count($current_page_orders) > 0): ?>
      <div class="orders-container">
        <table class="table table-hover mb-0">
          <thead>
            <tr>
              <th>Order ID</th>
              <th>Date & Time</th>
              <th>Location</th>
              <th>Status</th>
              <th>Total</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($current_page_orders as $order): ?>
              <tr class="order-row" data-bs-toggle="collapse" data-bs-target="#details-<?= $order['id'] ?>" aria-expanded="false">
                <td>#<?= $order['id'] ?></td>
                <td><?= date('Y/m/d h:i A', strtotime($order['order_date'])) ?></td>
                <td>
                  <?= isset($rooms[$order['room_id']]) ? htmlspecialchars($rooms[$order['room_id']]) : 'N/A' ?>
                </td>
                <td>
                  <?php 
                    $statusClass = '';
                    switch(strtolower($order['status'])) {
                      case 'pending':
                        $statusClass = 'status-pending';
                        break;
                      case 'preparing':
                        $statusClass = 'status-preparing';
                        break;
                      case 'delivered':
                        $statusClass = 'status-delivered';
                        break;
                      case 'cancelled':
                        $statusClass = 'status-cancelled';
                        break;
                    }
                  ?>
                  <span class="status-badge <?= $statusClass ?>">
                    <?= ucfirst(htmlspecialchars($order['status'])) ?>
                  </span>
                </td>
                <td>EGP <?= number_format($order['total'], 2) ?></td>
                <td>
                  <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#details-<?= $order['id'] ?>" aria-expanded="false">
                    <i class="fas fa-eye me-1"></i> Details
                  </button>
                  
                  <?php if(strtolower($order['status']) === 'pending'): ?>
                    <button class="btn btn-sm cancel-btn" type="button"
                      onclick="event.stopPropagation();" 
                      data-bs-toggle="modal" 
                      data-bs-target="#cancelModal"
                      data-order-id="<?= $order['id'] ?>">
                      <i class="fas fa-times me-1"></i> Cancel
                    </button>
                  <?php endif ?>
                </td>
              </tr>
              <tr class="collapse" id="details-<?= $order['id'] ?>">
                <td colspan="6" class="p-0">
                  <div class="card m-3 order-details">
                    <div class="card-header d-flex justify-content-between align-items-center">
                      <span>Order Details</span>
                      <?php if(!empty($order['notes'])): ?>
                        <span class="badge bg-info">Has Notes</span>
                      <?php endif; ?>
                    </div>
                    <div class="card-body">
                      <?php if(!empty($order['notes'])): ?>
                        <div class="alert alert-info mb-3">
                          <strong>Notes:</strong> <?= htmlspecialchars($order['notes']) ?>
                        </div>
                      <?php endif; ?>
                      
                      <h6>Items Ordered:</h6>
                      <div class="table-responsive">
                        <table class="table table-sm">
                          <thead>
                            <tr>
                              <th>Product</th>
                              <th>Quantity</th>
                              <th>Unit Price</th>
                              <th>Subtotal</th>
                            </tr>
                          </thead>
                          <tbody>
                          <?php
                            $item_query = "SELECT p.product_name, p.image, oi.quantity, p.price 
                                          FROM order_items oi
                                          JOIN products p ON p.id=oi.product_id
                                          WHERE oi.order_id='" . mysqli_real_escape_string($myconnection, $order['id']) . "'";
                            $item_result = mysqli_query($myconnection, $item_query);
                            
                            if (!$item_result) {
                              echo "<tr><td colspan='4'>Error loading order details: " . mysqli_error($myconnection) . "</td></tr>";
                            } else {
                              $subtotal = 0;
                              while($item = mysqli_fetch_assoc($item_result)):
                                $item_total = $item['quantity'] * $item['price'];
                                $subtotal += $item_total;
                          ?>
                            <tr>
                              <td class="d-flex align-items-center">
                                <?php if (!empty($item['image'])): ?>
                                  <img src="assets/products/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['product_name']) ?>" class="product-img me-2">
                                <?php else: ?>
                                  <div class="product-img bg-light d-flex align-items-center justify-content-center me-2">
                                    <i class="fas fa-coffee text-muted"></i>
                                  </div>
                                <?php endif; ?>
                                <?= htmlspecialchars($item['product_name']) ?>
                              </td>
                              <td><?= (int)$item['quantity'] ?></td>
                              <td>EGP <?= number_format($item['price'], 2) ?></td>
                              <td>EGP <?= number_format($item_total, 2) ?></td>
                            </tr>
                          <?php 
                              endwhile;
                              if (mysqli_num_rows($item_result) == 0) {
                                echo "<tr><td colspan='4'>No items found for this order</td></tr>";
                              }
                            }
                          ?>
                          </tbody>
                          <tfoot>
                            <tr>
                              <th colspan="3" class="text-end">Total:</th>
                              <th>EGP <?= number_format($order['total'], 2) ?></th>
                            </tr>
                          </tfoot>
                        </table>
                      </div>
                    </div>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      
      <?php if($total_pages > 1): ?>
        <nav aria-label="Orders pagination">
          <ul class="pagination">
            <li class="page-item <?= ($current_page <= 1) ? 'disabled' : '' ?>">
              <a class="page-link" href="?page=<?= $current_page-1 ?><?= $from ? '&date_from='.$from : '' ?><?= $to ? '&date_to='.$to : '' ?><?= $status_filter ? '&status='.$status_filter : '' ?>" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
              </a>
            </li>
            
            <?php for($i = 1; $i <= $total_pages; $i++): ?>
              <li class="page-item <?= ($current_page == $i) ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?><?= $from ? '&date_from='.$from : '' ?><?= $to ? '&date_to='.$to : '' ?><?= $status_filter ? '&status='.$status_filter : '' ?>">
                  <?= $i ?>
                </a>
              </li>
            <?php endfor; ?>
            
            <li class="page-item <?= ($current_page >= $total_pages) ? 'disabled' : '' ?>">
              <a class="page-link" href="?page=<?= $current_page+1 ?><?= $from ? '&date_from='.$from : '' ?><?= $to ? '&date_to='.$to : '' ?><?= $status_filter ? '&status='.$status_filter : '' ?>" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
              </a>
            </li>
          </ul>
        </nav>
      <?php endif; ?>
      
      
    <?php else: ?>
      <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>
        No orders found. Try adjusting your filter or place a new order.
      </div>
      <div class="text-center mt-4">
        <a href="index.php" class="btn btn-primary btn-lg">
          <i class="fas fa-shopping-cart me-2"></i>Order Now
        </a>
      </div>
    <?php endif; ?>
  </div>

  
  <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-confirm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="cancelModalLabel">Cancel Order</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>Are you sure you want to cancel this order? This action cannot be undone.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No, Keep Order</button>
          <a href="#" id="confirmCancelBtn" class="btn btn-danger">Yes, Cancel Order</a>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    
    document.addEventListener('DOMContentLoaded', function() {
      var cancelModal = document.getElementById('cancelModal');
      if (cancelModal) {
        cancelModal.addEventListener('show.bs.modal', function(event) {
          
          var button = event.relatedTarget;
          
          
          var orderId = button.getAttribute('data-order-id');
          
          
          var confirmButton = document.getElementById('confirmCancelBtn');
          confirmButton.href = 'myorder.php?cancel_id=' + orderId;
        });
      }
    });
  </script>
</body>
</html>
<?php
mysqli_close($myconnection);
?>