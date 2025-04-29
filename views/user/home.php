<?php
session_start();
include_once "../../connection.php";
include_once(__DIR__ .'/../../models/user.php');
include_once(__DIR__ .'/../../models/product.php');
include_once(__DIR__ .'/../../models/category.php');

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit; 
}

if (isset($_POST['logout'])) {
  session_destroy();
  header("Location: login.php");
}

$user_id = $_SESSION['user_id'];
$user=getUserById($user_id);

$itemsPerPage = 6;
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($currentPage - 1) * $itemsPerPage;

$allProducts = getAllAvaliableProducts();
$totalProducts = mysqli_num_rows($allProducts);
$totalPages = ceil($totalProducts / $itemsPerPage);
$sql = "SELECT * FROM products WHERE availability=1 ORDER BY product_name ASC LIMIT $itemsPerPage OFFSET $offset";
include(__DIR__ . '/../../connection.php');
$products = mysqli_query($myconnection, $sql);
$allCategories=getCategories();
$categoryFilter = isset($_POST['category']) ? intval($_POST['category']) : 0;

$sql = "SELECT * FROM products WHERE availability = 1";
$countSql = "SELECT COUNT(*) FROM products WHERE availability = 1";

if ($categoryFilter > 0) {
    $sql .= " AND category_id = $categoryFilter";
    $countSql .= " AND category_id = $categoryFilter";
}

$sql .= " ORDER BY product_name ASC LIMIT $itemsPerPage OFFSET $offset";

$products = mysqli_query($myconnection, $sql);
$resultCount = mysqli_query($myconnection, $countSql);
$totalProducts = mysqli_fetch_row($resultCount)[0];
$totalPages = ceil($totalProducts / $itemsPerPage);


?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Home Page</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
  :root {
  --primary: #6F4E37;
  --secondary: #C4A484;
  --light-brown: #e6d7c3;
  --dark-brown: #5a3c2a;
  --accent-color: #8B5A2B;
  --danger-color: #A52A2A;
  --text-color: #343a40;
  --title-color: #5a3c2a;
}

body {
  font-family: 'Nunito', sans-serif;
}

.header {
  background-color: var(--primary);
  color: var(--light-brown);
  padding: 15px 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  border-radius: 8px;
}

.header a {
  color: var(--light-brown);
  text-decoration: none;
  font-weight: bold;
  margin-right: 20px;
  transition: color 0.3s ease;
}

.header a:hover {
  color: var(--secondary);
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
  border: 2px solid var(--secondary);
}

.rounded-circle {
  width: 50px;
  height: 50px;
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
    
    
.card {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  border: none;
  border-radius: 20px;
  width: 250px;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  background-color: var(--light-brown);
}

.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
}

.card .card-footer {
  background-color: var(--light-brown);
  border-top: 1px solid var(--secondary);
  text-align: center;
  padding: 10px;
}

.card .card-footer p {
  margin: 0;
  font-weight: bold;
  color: var(--dark-brown);
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


.cart-item {
  border-bottom: 1px solid var(--secondary);
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
  color: var(--dark-brown);
}

.cart-item .badge {
  font-size: 1rem;
  background-color: var(--primary);
  color: var(--light-brown);
  border-radius: 8px;
  padding: 5px 10px;
}

.cart-total {
  font-size: 1.5rem;
  font-weight: bold;
  color: var(--primary);
  text-align: center;
  margin-top: 15px;
}

.btn-primary {
  background-color: var(--primary);
  border: none;
  transition: background-color 0.3s ease, transform 0.3s ease;
  color: var(--light-brown);
}

.btn-primary:hover {
  background-color: var(--dark-brown);
  transform: scale(1.05);
  color: var(--light-brown);
}

.btn-outline-secondary {
  color: var(--primary);
  border-color: var(--primary);
  transition: color 0.3s ease, background-color 0.3s ease;
}

.btn-outline-secondary:hover {
  color: var(--light-brown);
  background-color: var(--primary);
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
$allRooms = getAllRooms();
if (isset($_POST['add_Order'])) {
    $notes = $_POST['notes'];
    $total = $_POST['total'];
    $room = $_POST['room'];
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
// if (isset($_POST['filter'])) {
//   $category_id = $_POST['category'];
//  $products=getProductsByCategory();
// }



?>

<body class="p-3">
<div class="header d-flex justify-content-between align-items-center p-2">
    <div class="d-flex align-items-center">
    <a class="navbar-brand" href="home.php">
    <i class="fa-solid fa-mug-saucer fs-3  p-2"> Café Laté
    </i>   <div>
    </a> 
      <a href="home.php">Home</a> |
      <a href="myorder.php">My Orders</a>
    </div>
  </div>

  <div class="dropdown">
    <div class="d-flex align-items-center" data-bs-toggle="dropdown" style="cursor: pointer;">
      <span class="me-2 text-light">
        <div>Hi!</div><?= $user['name'] ?>
      </span>
      <img src="<?= $user['image']?>" class="rounded-circle" alt="User" width="40" height="40">
    </div>
    <ul class="dropdown-menu dropdown-menu-end">
      <li><form method="POST">  
      <button type="submit" class="bg-light" style="border:none;" name="logout">
    <a class="dropdown-item text-danger">Log Out</a>
                  </button> </form></li>

    </ul>
  </div>
</div>
<div class="container-fluid py-5">

  <div class="row" >

    <div class="col-md-4 ">
      <form class="border p-3 mb-3" method="POST">
        <div id="cart-items" class="mb-3">
          <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
            <h5 class="mb-4 text-dark fs-2 fw-bold ">Your Order List:</h5>
            <div class="list-group mb-3">
              <?php foreach ($_SESSION['cart'] as $index => $item): ?>
                <?php
                  $product = getProductById((int)$item['id']);
                ?>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                  <div>
                    <strong class="fs-4 fw-bold" style="color:var(--primary);"><?= $product['product_name'] ?></strong><br>
                    <strong class="text-dark fs-5 pt-3">  Price:</strong> <strong class="fs-4 fw-bold "><?= $product['price'] ?> LE<br> </strong>
                    <div class="d-flex align-items-center pt-3">
                      <button type="submit" name="update_quantity" value="decrease_<?= $index ?>" class="btn btn-sm btn-outline-secondary">-</button>
                      <span class="mx-2"><?= $item['quantity'] ?></span>
                      <button type="submit" name="update_quantity" value="increase_<?= $index ?>" class="btn btn-sm btn-outline-secondary">+</button>
                    </div>
                  </div>
                  <span class="badge btn-primary rounded-pill fs-5"><?= $product['price'] * $item['quantity'] ?> LE</span>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
        <div class="text-center my-5">
          <p class="-primary fs-4">
            <i class="fas fa-shopping-cart me-2"></i>
            Your order list is empty.
          </p>
          </div>
          <?php endif; ?>
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
      <div class="filter-form content-center mb-3">
      <form class="row g-3 align-items-end" method="POST"> 
   
      <div class="mb-2">
          <label for="category" class="form-label fs-5 fw-bold">Filter With Category</label>
          <select id="category" name="category" class="form-select combo-box">
            <?php foreach($allCategories as $index=>$item): ?>
              <option value="<?= $item['id'] ?>"><?= $item['category_name'] ?></option>
              <?php endforeach; ?>
          </select>
        </div>
   
        <div class="col-md-4 col-sm-12 d-flex gap-2">
          <button type="submit" name="filter" class="btn btn-primary flex-grow-1" >
            <i class="fas fa-filter me-2"></i>Filter
          </button>
          <a href="home.php" class="btn btn-outline-secondary flex-grow-1">
            <i class="fas fa-redo me-2"></i>Reset
          </a>
        </div>
      </form>
    </div>

      <div class="row">
        <?php while($product = mysqli_fetch_assoc($products)): ?>
          <div class="col-md-4 mb-4 mt-2">  
            <div class="card bg-light">
              <img src=<?= '../admin/'.$product['image'] ?> alt="product" class="product-image">
              <div class="card-body d-flex flex-column justify-content-between bg-dark">
                <h6 class="card-title text-light fs-4 fw-bold"><?= $product['product_name'] ?></h6>
              </div>
              <div class="card-footer d-flex justify-content-between align-items-center mt-2 mb-2">
                <p class="primary mb-0 card-title mt-2 mb-2 fs-4 fw-bold"><?= $product['price'] ?> LE</p>
                <form method="POST">
                  <input type="hidden" name="productId" value="<?= $product['id'] ?>">
                  <button type="submit" name="addToOrder" class="btn btn-primary btn-sm add" data-id="<?= $product['id'] ?>" data-name="<?= $product['product_name'] ?>" data-price="<?= $product['price'] ?>">
                    <i class="fa-solid fa-cart-shopping "></i>
                  </button>
                </form>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      </div>
    </div>
    <?php if ($totalPages > 1): ?>
                    <nav aria-label="Page navigation" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <li class="page-item <?= $currentPage == 1 ? 'disabled' : '' ?>">
                                <a class="page-link " href="?page=<?= $currentPage - 1 ?>" aria-label="Previous">
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
</body>
</html>