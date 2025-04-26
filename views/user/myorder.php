<?php
session_start();
include_once "../../connection.php";
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}
$user_id = $_SESSION['user_id'];

if (isset($_GET['cancel_id'])) {
  $cancel_id = mysqli_real_escape_string($myconnection, $_GET['cancel_id']);
  $query = "UPDATE order_items SET status='cancelled' WHERE order_id='$cancel_id' AND status='pending'";
  $result = mysqli_query($myconnection, $query);
  if ($result) {
    echo "success";
  } else {
     echo "Error cancelling order: " . mysqli_error($myconnection);
  }
  header("Location: orders.php");
  exit;
}

$from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$to = isset($_GET['date_to']) ? $_GET['date_to'] : '';
$where = "o.user_id = '$user_id'";

if ($from) {
  $from = mysqli_real_escape_string($myconnection, $from);
  $where .= " AND o.created_at >= '$from 00:00:00'";
}
if ($to) {
  $to = mysqli_real_escape_string($myconnection, $to);
  $where .= " AND o.created_at <= '$to 23:59:59'";
}

$sql = "
  SELECT o.id, o.created_at AS order_date, 
         CASE 
           WHEN EXISTS (SELECT 1 FROM order_items WHERE order_id = o.id AND status = 'cancelled') THEN 'cancelled'
           WHEN EXISTS (SELECT 1 FROM order_items WHERE order_id = o.id AND status = 'pending') THEN 'pending'
           WHEN EXISTS (SELECT 1 FROM order_items WHERE order_id = o.id AND status = 'preparing') THEN 'preparing'
           ELSE 'delivered'
         END AS status,
         o.total
  FROM orders o
  WHERE $where
  ORDER BY o.created_at DESC
";

$result = mysqli_query($myconnection, $sql);
if (!$result) {
  die("Query failed: " . mysqli_error($myconnection));
}

$orders = [];
while ($row = mysqli_fetch_assoc($result)) {
  $orders[] = $row;
}

$user_query = "SELECT name FROM users WHERE id = '$user_id'";
$user_result = mysqli_query($myconnection, $user_query);
$user_name = "";
if ($user_result && mysqli_num_rows($user_result) > 0) {
  $user_data = mysqli_fetch_assoc($user_result);
  $user_name = $user_data['name'];
}


$items_per_page = 5;
$total_items = count($orders);
$total_pages = ceil($total_items / $items_per_page);
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($current_page - 1) * $items_per_page;

$current_page_orders = array_slice($orders, $offset, $items_per_page);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>My Orders</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary:rgb(82, 148, 176);
      --primary-dark: rgb(82, 148, 176);
      --secondary:rgb(95, 56, 10);
      --secondary-light:rgb(81, 51, 10);
      --light-bg: #f9f7ff;
      --dark-text: #333333;
      --light-text: #ffffff;
      --neutral-light: #f0f2f5;
      --neutral: #e1e3ea;
      --neutral-dark: #B0B7C3;
      --success:rgb(70, 196, 74);
      --warning:rgb(100, 65, 12);
      --danger: #f44336;
      --info: #2196F3;
    }
    
    body {
      font-family: 'Poppins', sans-serif;
      background-color: var(--light-bg);
      color: var(--dark-text);
    }
    
    
    
   
    
    
    
    .user-avatar {
      width: 42px;
      height: 42px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid var(--secondary);
      box-shadow: 0 3px 8px rgba(0,0,0,0.15);
      transition: all 0.3s;
    }
    
    .user-avatar:hover {
      transform: scale(1.05);
      border-color: var(--secondary-light);
    }
    
    .user-name {
      font-size: 0.95rem;
      font-weight: 500;
      margin-right: 15px;
      color: var(--light-text);
    }
    
    .page-header {
      background-color: white;
      border-radius: 16px;
      padding: 24px;
      margin-bottom: 30px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.05);
      display: flex;
      align-items: center;
    }
    
    .page-header h1 {
      font-weight: 600;
      font-size: 1.8rem;
      margin: 0;
      color: var(--primary);
    }
    
    .page-header i {
      font-size: 2rem;
      color: var(--secondary);
      margin-right: 15px;
    }
    
    .card {
      border: none;
      border-radius: 16px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.05);
      overflow: hidden;
      margin-bottom: 30px;
    }
    
    .card-header {
      background-color: white;
      border-bottom: 1px solid var(--neutral);
      padding: 18px 24px;
    }
    
    .filter-form {
      background-color: white;
      border-radius: 16px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.05);
      padding: 24px;
      margin-bottom: 30px;
    }
    
    .form-label {
      font-weight: 500;
      font-size: 0.9rem;
      color: var(--primary);
    }
    
    .form-control {
      border-radius: 8px;
      padding: 10px 16px;
      border: 1px solid var(--neutral);
    }
    
    .form-control:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 0.25rem rgba(78, 65, 135, 0.25);
    }
    
    .btn {
      padding: 10px 20px;
      border-radius: 8px;
      font-weight: 500;
      transition: all 0.3s;
    }
    
    .btn-primary {
      background-color: var(--primary);
      border-color: var(--primary);
    }
    
    .btn-primary:hover {
      background-color: var(--primary-dark);
      border-color: var(--primary-dark);
    }
    
    .btn-outline-secondary {
      color: var(--primary);
      border-color: var(--neutral);
    }
    
    .btn-outline-secondary:hover {
      background-color: var(--neutral-light);
      color: var(--primary);
      border-color: var(--neutral);
    }
    
    .order-table {
      background-color: white;
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    
    .table {
      margin-bottom: 0;
    }
    
    .table th {
      font-weight: 600;
      background-color: var(--neutral-light);
      color: var(--primary);
      padding: 16px 24px;
      font-size: 0.9rem;
      border-bottom: 1px solid var(--neutral);
    }
    
    .table td {
      padding: 16px 24px;
      vertical-align: middle;
      border-bottom: 1px solid var(--neutral);
      font-size: 0.95rem;
    }
    
    .order-row {
      cursor: pointer;
      transition: background-color 0.3s;
    }
    
    .order-row:hover {
      background-color: var(--neutral-light);
    }
    
    .status-badge {
      display: inline-flex;
      align-items: center;
      padding: 6px 12px;
      border-radius: 50px;
      font-weight: 500;
      font-size: 0.85rem;
    }
    
    .status-pending {
      background-color: rgba(33, 150, 243, 0.1);
      color: var(--info);
    }
    
    .status-preparing {
      background-color: rgba(255, 152, 0, 0.1);
      color: var(--warning);
    }
    
    .status-delivered {
      background-color: rgba(76, 175, 80, 0.1);
      color: var(--success);
    }
    
    .status-cancelled {
      background-color: rgba(244, 67, 54, 0.1);
      color: var(--danger);
    }
    
    .cancel-btn {
      background-color: var(--danger);
      color: white;
      border: none;
      padding: 6px 16px;
      border-radius: 8px;
      font-weight: 500;
      font-size: 0.85rem;
      transition: all 0.3s;
    }
    
    .cancel-btn:hover {
      background-color: #d32f2f;
      box-shadow: 0 3px 8px rgba(244, 67, 54, 0.3);
    }
    
    .expand-icon {
      transition: transform 0.3s;
      font-size: 0.8rem;
      width: 20px;
      height: 20px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background-color: var(--neutral-light);
      border-radius: 50%;
      margin-left: 8px;
    }
    
    .rotate-180 {
      transform: rotate(180deg);
    }
    
    .product-img {
      width: 40px;
      height: 40px;
      object-fit: cover;
      border-radius: 8px;
      margin-right: 12px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    .order-details {
      background-color: var(--neutral-light);
    }
    
    .order-details-table {
      background-color: white;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 3px 10px rgba(0,0,0,0.05);
    }
    
    .order-details-table th {
      background-color: var(--neutral-light);
      color: var(--primary);
      font-weight: 500;
      padding: 12px 16px;
      font-size: 0.85rem;
    }
    
    .order-details-table td {
      padding: 12px 16px;
      font-size: 0.9rem;
      border-bottom: 1px solid var(--neutral-light);
    }
    
    .total-row {
      font-weight: 600;
      background-color: var(--neutral-light);
    }
    
    .total-row th, .total-row td {
      padding: 16px 24px;
    }
    
    .pagination {
      justify-content: center;
      margin-top: 30px;
    }
    
    .page-link {
      color: var(--primary);
      padding: 10px 16px;
      margin: 0 3px;
      border-radius: 8px;
      border: 1px solid var(--neutral);
    }
    
    .page-link:hover {
      background-color: var(--neutral-light);
      color: var(--primary);
      border-color: var(--neutral);
    }
    
    .page-item.active .page-link {
      background-color: var(--primary);
      border-color: var(--primary);
    }
    
    .page-item.disabled .page-link {
      color: var(--neutral-dark);
      pointer-events: none;
      background-color: white;
      border-color: var(--neutral);
    }
    
    .alert {
      border-radius: 12px;
      padding: 16px 24px;
      border: none;
      box-shadow: 0 3px 10px rgba(0,0,0,0.05);
    }
    
    .alert-info {
      background-color: rgba(33, 150, 243, 0.1);
      color: var(--info);
    }
    
   
    @media (max-width: 768px) {
      
      .page-header {
        padding: 16px;
        margin-bottom: 20px;
      }
      
      .page-header h1 {
        font-size: 1.5rem;
      }
      
      .page-header i {
        font-size: 1.6rem;
      }
      
      .filter-form {
        padding: 16px;
        margin-bottom: 20px;
      }
      
      .table th, .table td {
        padding: 12px 16px;
      }
    }
  </style>
</head>
<body>
 
  <div class="container py-5">
    <div class="page-header">
      <h1>My Orders</h1>
    </div>

    <div class="filter-form">
      <form class="row g-3 align-items-end" method="get">
        <div class="col-md-4 col-sm-6">
          <label for="date_from" class="form-label"><i class="far fa-calendar-alt me-2"></i>Date from</label>
          <input type="date" id="date_from" name="date_from" class="form-control" value="<?= htmlspecialchars($from) ?>">
        </div>
        <div class="col-md-4 col-sm-6">
          <label for="date_to" class="form-label"><i class="far fa-calendar-alt me-2"></i>Date to</label>
          <input type="date" id="date_to" name="date_to" class="form-control" value="<?= htmlspecialchars($to) ?>">
        </div>
        <div class="col-md-4 col-sm-12 d-flex gap-2">
          <button type="submit" class="btn btn-primary flex-grow-1">
            <i class="fas fa-filter me-2"></i>Filter
          </button>
          <a href="myorder.php?date_from=&date_to=" class="btn btn-outline-secondary flex-grow-1">
            <i class="fas fa-redo me-2"></i>Reset
          </a>
        </div>
      </form>
    </div>

    <?php if (count($current_page_orders) > 0): ?>
    <div class="order-table">
      <table class="table table-hover mb-0">
        <thead>
          <tr>
            <th>Order Date</th>
            <th>Status</th>
            <th>Amount</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($current_page_orders as $o): ?>
          <tr class="order-row" data-bs-toggle="collapse" data-bs-target="#details-<?= $o['id'] ?>" aria-expanded="false">
            <td>
              <?= date('Y/m/d h:i A', strtotime($o['order_date'])) ?>
            </td>
            <td>
              <?php 
                $statusClass = '';
                $statusIcon = '';
                switch(strtolower($o['status'])) {
                  case 'pending':
                    $statusClass = 'status-pending';
                    $statusIcon = 'fa-clock';
                    break;
                  case 'preparing':
                    $statusClass = 'status-preparing';
                    $statusIcon = 'fa-mug-hot';
                    break;
                  case 'delivered':
                    $statusClass = 'status-delivered';
                    $statusIcon = 'fa-check-circle';
                    break;
                  case 'cancelled':
                    $statusClass = 'status-cancelled';
                    $statusIcon = 'fa-times-circle';
                    break;
                }
              ?>
              <span class="status-badge <?= $statusClass ?>">
                <i class="fas <?= $statusIcon ?> me-2"></i>
                <?= htmlspecialchars(ucfirst($o['status'])) ?>
              </span>
            </td>
            <td>EGP <?= number_format($o['total'],2) ?></td>
            <td>
              <?php if(strtolower($o['status']) === 'pending'): ?>
                <button class="cancel-btn" 
                  onclick="event.stopPropagation(); if(confirm('Are you sure you want to cancel this order?')) window.location.href='?cancel_id=<?= $o['id'] ?>'">
                  <i class="fas fa-times me-1"></i> CANCEL
                </button>
              <?php endif ?>
            </td>
          </tr>
          <tr class="collapse order-details" id="details-<?= $o['id'] ?>">
            <td colspan="4" class="p-3">
              <div class="order-details-table">
                <table class="table mb-0">
                  <thead>
                    <tr>
                      <th>Product</th>
                      <th>Room</th>
                      <th>Qty</th>
                      <th>Status</th>
                      <th>Price</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php
                    $item_query = "SELECT p.product_name, p.image, p.price, r.room_name, oi.quantity, oi.status
                                  FROM order_items oi
                                  JOIN products p ON p.id=oi.product_id
                                  JOIN rooms r ON r.id=oi.room_id
                                  WHERE oi.order_id='" . mysqli_real_escape_string($myconnection, $o['id']) . "'";
                    $item_result = mysqli_query($myconnection, $item_query);
                    
                    if (!$item_result) {
                      echo "<tr><td colspan='5'>Error loading order details: " . mysqli_error($myconnection) . "</td></tr>";
                    } else {
                      while($item = mysqli_fetch_assoc($item_result)):
                  ?>
                    <tr class="order-item-row">
                      <td class="d-flex align-items-center">
                        <?php if (!empty($item['image'])): ?>
                          <img src="uploads/products/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['product_name']) ?>" class="product-img">
                        <?php else: ?>
                          <div class="product-img bg-light d-flex align-items-center justify-content-center">
                            <i class="fas fa-coffee text-muted"></i>
                          </div>
                        <?php endif; ?>
                        <?= htmlspecialchars($item['product_name']) ?>
                      </td>
                      <td><?= htmlspecialchars($item['room_name']) ?></td>
                      <td><?= (int)$item['quantity'] ?></td>
                      <td>
                        <?php 
                          $itemStatusClass = '';
                          switch(strtolower($item['status'])) {
                            case 'pending':
                              $itemStatusClass = 'text-info';
                              break;
                            case 'preparing':
                              $itemStatusClass = 'text-warning';
                              break;
                            case 'delivered':
                              $itemStatusClass = 'text-success';
                              break;
                            case 'cancelled':
                              $itemStatusClass = 'text-danger';
                              break;
                          }
                        ?>
                        <span class="<?= $itemStatusClass ?>"><?= htmlspecialchars(ucfirst($item['status'])) ?></span>
                      </td>
                      <td>EGP <?= number_format($item['price'],2) ?></td>
                    </tr>
                  <?php 
                      endwhile;
                      if (mysqli_num_rows($item_result) == 0) {
                        echo "<tr><td colspan='5'>No items found for this order</td></tr>";
                      }
                    }
                  ?>
                  </tbody>
                </table>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
        <tfoot>
          <tr class="total-row">
            <th colspan="2">Total (Current Page)</th>
            <th colspan="2">
              <?php
              $total = 0;
              foreach ($current_page_orders as $order) {
                $total += $order['total'];
              }
              ?>
              EGP <?= number_format($total, 2) ?>
            </th>
          </tr>
        </tfoot>
      </table>
    </div>
    
    <?php if($total_pages > 1): ?>
   
    <?php endif; ?>
    
    <?php else: ?>
      <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>
        No orders found. Try adjusting your filter or create a new order.
      </div>
    <?php endif; ?>
  </div>

  

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(item => {
      item.addEventListener('click', event => {
        const icon = item.querySelector('.expand-icon i');
        if (icon) {
          icon.closest('.expand-icon').classList.toggle('rotate-180');
        }
      })
    });
  </script>
</body>
</html>
<?php
mysqli_close($myconnection);
?>