<?php
session_start(); // FIRST LINE
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Home Page</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
      .product-image {
      height: 150px;
      object-fit: cover;
      border-radius: 8px;
    }
    .card {
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s ease;
    }
    .card:hover {
      transform: scale(1.05);
    }
    .cart-item {
      border-bottom: 1px solid #ddd;
      padding: 10px 0;
    }
    .cart-item:last-child {
      border-bottom: none;
    }
    .cart-total {
      font-size: 1.25rem;
      font-weight: bold;
    }
    .header {
      background-color: #343a40;
      color: #fff;
      padding: 15px 20px;
    }
    .header a {
      color: #fff;
      text-decoration: none;
    }
    .header a:hover {
      text-decoration: underline;
    }
  </style>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<?php

include_once(__DIR__ .'/../../models/product.php');
include_once(__DIR__ .'/../../models/room.php');
include_once(__DIR__ .'/../../models/order.php');
include_once(__DIR__ .'/../connection.php');

$allProducts = getAllProducts();
$allRooms = getAllRooms();
  if (isset($_POST['add_Order'])) {
      $notes = $_POST['notes'];
      $total = $_POST['total'];
      $room = $_POST['room'];
      $order_id=addOrder(1, $room,$total, 'pending', $notes);
      if ($order_id) {
          if (addOrderItems($order_id)) {
      echo "<div class='alert alert-success'>Your order has been placed successfully!</div>";
      unset($_SESSION['cart']);            

          } else {
              echo "<div class='alert alert-danger'>There was an issue adding your items to the order.</div>";
          }

  }}
  
  if (isset($_POST['update_quantity'])) {
    list($action, $index) = explode('_', $_POST['update_quantity']);
      if (isset($_SESSION['cart'][$index])) {
          if ($action == 'increase' && $_SESSION['cart'][$index]['quantity'] < 10) {
              $_SESSION['cart'][$index]['quantity']++;
          }

      elseif ($action == 'decrease') {
          if ($_SESSION['cart'][$index]['quantity'] > 1) {
            $_SESSION['cart'][$index]['quantity']--;
        } else {
            array_splice($_SESSION['cart'], $index, 1);
        }
        
    }}
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
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }
}
?>

<body class="p-3">
<div class="container-fluid">
  <!-- Header -->
  <div class="header d-flex justify-content-between align-items-center">
    <div>
      <a href="#">Home</a> |
      <a href="#">My Orders</a>
    </div>
    <div class="d-flex align-items-center">
      <span class="me-2">omar khaled</span>
      <img src="https://via.placeholder.com/30" class="rounded-circle" alt="User">
    </div>
    </div>

  <div class="row">
  <div class="col-md-4">  
    <form class="border p-3 mb-3" method="POST">
  
  <div id="cart-items" class="mb-3">
    <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
      <h5 class="mb-3">Your Cart:</h5>
      <div class="list-group mb-3">
        <?php foreach ($_SESSION['cart'] as $index => $item): ?>
          <?php 
            // Fetch product details using function
            $product = getProductById((int)$item['id']); 
          ?>
          <div class="list-group-item d-flex justify-content-between align-items-center">
            <div>
              <strong><?= $product['product_name'] ?></strong><br>
              Price: <?= $product['price'] ?> LE<br>

              <!-- Quantity Adjustment -->
              Quantity: 
              <div class="d-flex align-items-center">
                <!-- Decrease Button -->
                <button type="submit" name="update_quantity" value="decrease_<?= $index ?>" class="btn btn-sm btn-outline-secondary">-</button>
                <span class="mx-2"><?= $item['quantity'] ?></span>
                <!-- Increase Button -->
                <button type="submit" name="update_quantity" value="increase_<?= $index ?>" class="btn btn-sm btn-outline-secondary">+</button>
              </div>
            </div>
            <span class="badge bg-primary rounded-pill"><?= $product['price'] * $item['quantity'] ?> LE</span>
          </div>
        <?php endforeach; ?>
      </div>
        <?php else: ?>
          <p>Your cart is empty.</p>
        <?php endif; ?>
      </div>
      

      <div class="mb-2">
            <label for="room" class="form-label">Room</label>
            <select id="room" name="room" class="form-select combo-box">
            <?php while($room = mysqli_fetch_assoc($allRooms)): ?>
            <option value="<?= $room['id'] ?>"><?= $room['room_name'] ?></option>
            <?php endwhile; ?>
        </select>
        </div>


    <div class="mb-2">
      <label for="notes" class="form-label">Notes</label>
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
      <!-- Confirm Button -->
      <button type="submit" name="add_Order" class="btn btn-primary w-100">Confirm</button>
      
    </form>
  </div>
</div>

  <!-- Latest Order Section -->
  <div class="col-md-8">
    <h5>Latest Order</h5>
    <div class="d-flex gap-3 mb-3">
      <div class="card text-center" style="width: 6rem;">
        <div class="card-body p-2">
          <h6 class="card-title mb-0">Tea</h6>
        </div>
      </div>   
    </div> 
  </div>
</div>

<br>
<hr>

    <div class="row">
  <?php while($product = mysqli_fetch_assoc($allProducts)): ?>
    <div class="col-md-4 mb-4">
      <div class="card">
        <img src="<?= $product['image'] ?>" alt="product" class="card-image">
        <div class="card-body d-flex flex-column justify-content-between bg-dark">
          <h6 class="card-title text-light"><?= $product['product_name'] ?></h6>
        </div>
        <div class="card-footer d-flex justify-content-between align-items-center mt-2 mb-2">
          <p class="text-primary mb-0 card-title mt-2 mb-2 "><?= $product['price'] ?> LE</p>
          <form method="POST">
          <input type="hidden" name="productId" value="<?= $product['id'] ?>">
          <button 
          type="submit"
           name="addToOrder"
            class="btn btn-outline-primary btn-sm add" 
            data-id="<?= $product['id'] ?>" 
            data-name="<?= $product['product_name'] ?>" 
            data-price="<?= $product['price'] ?>"
          >
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
<script>
  
</script>

</body>
</html>