<?php
session_start();
include_once "../../connection.php";
include_once(__DIR__ .'/../../models/product.php');
$itemsPerPage = 6;
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($currentPage - 1) * $itemsPerPage;

$allProducts = getAllAvaliableProducts();
$totalProducts = mysqli_num_rows($allProducts);
$totalPages = ceil($totalProducts / $itemsPerPage);
$sql = "SELECT * FROM products WHERE availability=1 ORDER BY product_name ASC LIMIT $itemsPerPage OFFSET $offset";
$products = mysqli_query($myconnection, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>add Order Page</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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

body {
  font-family: 'Nunito', sans-serif;
  background-color: var(--secondary-color);
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
        

.header {
  background-color: var(--primary-color);
  color: var(--secondary-color);
  padding: 15px 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  border-radius: 8px;
}

.header a {
  color: var(--secondary-color);
  text-decoration: none;
  font-weight: bold;
  margin-right: 20px;
  transition: color 0.3s ease;
}

.header a:hover {
  color: var(--accent-color);
}

.header .user-info {
  display: flex;
  align-items: center;
}

.header .user-info img {
  border-radius: 50%;
  margin-left: 10px;
  width: 35px;
  height: 35px;
  border: 2px solid var(--accent-color);
}
.rounded-circle{
  width: 50px;
  height: 50px;
}

.card {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  border: none;
  border-radius: 20px;
  width: 250px;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  background-color: #fff;
}

.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
}

.card .card-footer {
  background-color: var(--secondary-color);
  border-top: 1px solid #e3e6f0;
  text-align: center;
  padding: 10px;
}

.card .card-footer p {
  margin: 0;
  font-weight: bold;
}

.card .card-footer .btn {
  margin-top: 5px;
}

.product-image {
  height: 150px;
  object-fit: cover;
  border-radius: 12px 12px 0 0;
  transition: transform 0.3s ease;
}

.card:hover .product-image {
  transform: scale(1.05);
}

.cart-item {
  border-bottom: 1px solid #ddd;
  padding: 15px 0;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.cart-item:last-child {
  border-bottom: none;
}

.cart-item strong {
  font-size: 1rem;
  color: var(--text-color);
}

.cart-item .badge {
  font-size: 1rem;
  background-color: var(--primary-color);
  color: var(--secondary-color);
  border-radius: 8px;
  padding: 5px 10px;
}

.cart-total {
  font-size: 1.5rem;
  font-weight: bold;
  color: var(--primary-color);
  text-align: center;
  margin-top: 15px;
}

.btn-primary {
  background-color: var(--primary-color);
  border: none;
  transition: background-color 0.3s ease, transform 0.3s ease;
}
.container-fluid {
  margin-left: 250px; /* Same as sidebar width */
  width: calc(100% - 250px);
}
.btn-primary:hover {
  background-color: #375a7f;
  transform: scale(1.05);
}

.btn-outline-secondary {
  color: var(--primary-color);
  border-color: var(--primary-color);
  transition: color 0.3s ease, background-color 0.3s ease;
}

.btn-outline-secondary:hover {
  color: var(--secondary-color);
  background-color: var(--primary-color);
}

@media (max-width: 768px) {
  .header {
    flex-direction: column;
    text-align: center;
  }

  .header a {
    margin: 5px 0;
  }

  .cart-item {
    flex-direction: column;
  }

  .cart-item strong,
  .cart-item .badge {
    margin-bottom: 10px;
  }
}
  </style>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<?php
include_once(__DIR__ .'/../../models/room.php');
include_once(__DIR__ .'/../../models/order.php');
include_once(__DIR__ .'/../../models/user.php');

$allRooms = getAllRooms();
$allUsers=getAllUsers();

if (isset($_POST['add_Order'])) {
    $notes = $_POST['notes'];
    $total = $_POST['total'];
    $room = $_POST['room'];
    $user_id = $_POST['user'];
    $order_id = addOrder($user_id, $room, $total, 'pending', $notes);
    if ($order_id) {
        if (addOrderItems($order_id)) {
          echo "<div class='modal fade' id='successModal' tabindex='-1' aria-labelledby='successModalLabel' aria-hidden='true'>
          <div class='modal-dialog modal-dialog-centered'>
            <div class='modal-content'>
              <div class='modal-header bg-success text-white'>
                <h5 class='modal-title' id='successModalLabel'>Success</h5>
                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
              </div>
              <div class='modal-body'>
                Your order has been placed successfully!
              </div>
              <div class='modal-footer'>
                <button type='button' class='btn btn-success' data-bs-dismiss='modal'>OK</button>
              </div>
            </div>
          </div>
        </div>
        <script>
          document.addEventListener('DOMContentLoaded', function () {
            const successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();
          });
        </script>"; 
        unset($_SESSION['cart']);
        } else {
          echo "<div class='modal fade' id='failureModal' tabindex='-1' aria-labelledby='failureModalLabel' aria-hidden='true'>
          <div class='modal-dialog modal-dialog-centered'>
            <div class='modal-content'>
              <div class='modal-header bg-danger text-white'>
                <h5 class='modal-title' id='failureModalLabel'>Error</h5>
                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
              </div>
              <div class='modal-body'>
                There was an issue adding your items to the order. Please try again.
              </div>
              <div class='modal-footer'>
                <button type='button' class='btn btn-danger' data-bs-dismiss='modal'>OK</button>
              </div>
            </div>
          </div>
        </div>
        <script>
          document.addEventListener('DOMContentLoaded', function () {
              const failureModal = new bootstrap.Modal(document.getElementById('failureModal'));
              failureModal.show();
          });
        </script>";  }
    }
}

if (isset($_POST['update_quantity'])) {
    list($action, $index) = explode('_', $_POST['update_quantity']);
    if (isset($_SESSION['cart'][$index])) {
        if ($action == 'increase' && $_SESSION['cart'][$index]['quantity'] < 10) {
            $_SESSION['cart'][$index]['quantity']++;
        } elseif ($action == 'decrease') {
            if ($_SESSION['cart'][$index]['quantity'] > 1) {
                $_SESSION['cart'][$index]['quantity']--;
            } else {
                array_splice($_SESSION['cart'], $index, 1);
            }
        }
    }
}
if (isset($_POST['addToOrder'])) {
    if (isset($_POST['productId'])) {
        $productId = $_POST['productId'];
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        $found = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['id'] == $productId) {
                $item['quantity'] += 1;
                $found = true;
                break;
            }
        }
        unset($item);
        if (!$found) {
            $_SESSION['cart'][] = [
                'id' => $productId,
                'quantity' => 1
            ];
        }
        echo '<script>window.location.href="'.$_SERVER['PHP_SELF'].'";</script>';
        exit();
    }
}
?>
<body class="p-3">
<div class="d-flex">
  
  <div class="sidebar bg-light p-3" style="width: 250px; min-height: 100vh;">
    <a href="#" class="sidebar-brand d-flex align-items-center justify-content-center mb-4">
      <i class="fas fa-store me-2"></i>
      <span>Admin Panel</span>
    </a>
    <div class="sidebar-divider"></div>
    <div class="nav flex-column">
      <a href="home.php" class="sidebar-item mb-2">
        <i class="fas fa-home"></i> <span>Home</span>
      </a>
      <a href="listProducts.php" class="sidebar-item mb-2">
        <i class="fas fa-box-open"></i> <span>Products</span>
      </a>
      <a href="users.php" class="sidebar-item mb-2">
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

<div class="container-fluid">
  <div class="row">

    <div class="col-md-4 ">
      <form class="border p-3 mb-3" method="POST">
        <div id="cart-items" class="mb-3">
          <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
            <h5 class="mb-4 text-dark fs-2 fw-bold">User  Order List:</h5>
            <div class="list-group mb-3">
              <?php foreach ($_SESSION['cart'] as $index => $item): ?>
                <?php
                  $product = getProductById((int)$item['id']);
                ?>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                  <div>
                    <strong class="text-primary fs-4"><?= $product['product_name'] ?></strong><br>
                    <strong class="text-dark fs-5 pt-3 fw-bold">  Price:</strong> <strong class="text-success fs-5 fw-bold"><?= $product['price'] ?> LE<br> </strong>
                    <div class="d-flex align-items-center pt-3">
                      <button type="submit" name="update_quantity" value="decrease_<?= $index ?>" class="btn btn-sm btn-outline-secondary">-</button>
                      <span class="mx-2"><?= $item['quantity'] ?></span>
                      <button type="submit" name="update_quantity" value="increase_<?= $index ?>" class="btn btn-sm btn-outline-secondary">+</button>
                    </div>
                  </div>
                  <span class="badge bg-primary rounded-pill fs-5"><?= $product['price'] * $item['quantity'] ?> LE</span>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
        <div class="text-center my-5">
          <p class="text-primary fs-4">
            <i class="fas fa-shopping-cart me-2"></i>
            User Order List is empty.
          </p>
          </div>
          <?php endif; ?>
        </div>

        <div class="mb-2">
        <label for="user" class="form-label fw-bold">Select a user to place the order for</label>
        <select id="user" name="user" class="form-select combo-box">
            <?php while($user = mysqli_fetch_assoc($allUsers)): ?>
              <option value="<?= $user['id'] ?>"><?= $user['name'] ?></option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="mb-2">
          <label for="room" class="form-label  fw-bold">Room</label>
          <select id="room" name="room" class="form-select combo-box">
            <?php while($room = mysqli_fetch_assoc($allRooms)): ?>
              <option value="<?= $room['id'] ?>"><?= $room['room_name'] ?></option>
            <?php endwhile; ?>
          </select>
        </div>

        <div class="mb-2">
          <label for="notes" class="form-label fw-bold">Notes</label>
          <textarea id="notes" name="notes" class="form-control" rows="2" placeholder="e.g. Extra sugar..."></textarea>
        </div>

        <?php
          function calculateCartTotal() {
              $subtotal = 0;
              if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
                  foreach ($_SESSION['cart'] as $item) {
                      $product = getProductById((int)$item['id']);
                      if ($product && isset($product['price'])) {
                          $subtotal += $product['price'] * $item['quantity'];
                      }
                  }
              }
              return $subtotal;
          }

          $total = calculateCartTotal();
        ?>

        <div class="mb-3 fw-bold" id="cart-total">
          <?php
            if ($total > 0) {
                echo "EGP " . number_format($total, 2);
            } else {
                echo "EGP 0.00";
            }
          ?>
        </div>

        <input type="hidden" name="total" value="<?= $total ?>">
        <button type="submit" name="add_Order" class="btn btn-primary w-100">Confirm</button>
      </form>
    </div>

    <div class="col-md-8 mt-3">
      <!-- <h5>Latest Order</h5>
      <div class="d-flex gap-3 mb-3">
        <div class="card text-center" style="width: 6rem;">
          <div class="card-body p-2">
            <h6 class="card-title mb-0">Tea</h6>
          </div>
        </div>
      </div> -->

      <div class="row">
        <?php while($product = mysqli_fetch_assoc($products)): ?>
          <div class="col-md-4 mb-4 mt-2">  
            <div class="card">
              <img src="<?=__DIR__.'/../../admin/'.$product['image'] ?>" alt="product" class="product-image">
              <div class="card-body d-flex flex-column justify-content-between bg-dark">
                <h6 class="card-title text-light"><?= $product['product_name'] ?></h6>
              </div>
              <div class="card-footer d-flex justify-content-between align-items-center mt-2 mb-2">
                <p class="text-primary mb-0 card-title mt-2 mb-2"><?= $product['price'] ?> LE</p>
                <form method="POST">
                  <input type="hidden" name="productId" value="<?= $product['id'] ?>">
                  <button type="submit" name="addToOrder" class="btn btn-outline-primary btn-sm add" data-id="<?= $product['id'] ?>" data-name="<?= $product['product_name'] ?>" data-price="<?= $product['price'] ?>">
                    <i class="fa-solid fa-cart-shopping"></i>
                  </button>
                </form>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      </div>
    </div>
   
            </div>
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
                        Showing <?= ($offset + 1) ?> to <?= min($offset + $itemsPerPage, $totalProducts) ?> of <?= $totalProducts ?> products
                    </div>
                <?php endif; ?>
        </div>
  </div>
</div>
</div>
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
    });
    document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.delete-btn').forEach(button => {
    button.addEventListener('click', function() {
        const productId = this.getAttribute('data-product-id');
        document.getElementById('deleteProductId').value = productId;
    });
});

$('#deleteConfirmModal').on('shown.bs.modal', function () {
    $('.btn-secondary').focus();
});
});
    </script>
</body>
</html>