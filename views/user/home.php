<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Home Page</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .cup-icon {
      font-size: 50px;
    }
    .combo-box {
      width: 100%;
    }
    .product-image {
      height: 50px;
      object-fit: contain;
    }
        .cards-container {
    display: flex;
    flex-direction: row;
    overflow-x: auto; 
    }
    .cards-container {

    display: flex;
    flex-wrap: wrap;
    justify-content: left;
    column-gap: 60px;
    
    row-gap : 40px;
    padding: 20px;

    

    }

    .card {
    background: transparent !important;
    width: 200px !important;
    height: 330px !important;
    position: relative;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.4);
    background-color: rgb(36, 36, 36);
    border-radius: 12px;
    overflow: hidden;
    color: white !important;
    transition: transform 0.3s ease;
    }

    .card:hover {
    transform: scale(1.05);
    box-shadow: 0 8px 16px rgba(3, 4, 32, 0.6);

    }

    .card-image {
    width: 100%;
    height:200px;
    object-fit: cover;
    }

    .card-footer {
    position:  relative;
    width: 100%;
    height: 0px;

    color: rgb(0, 0, 0) !important;
    padding: 10px;
    box-sizing: border-box;

    bottom: 0;
    }

    .card-title {
    margin: 0;
    font-size: 18px;
    font-family: sans-serif;
    }
  </style>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<?php 
include_once(__DIR__ . '/../../models/product.php');
include_once(__DIR__ . '/../../models/room.php');
include_once(__DIR__ . '/../../connection.php');
$allProducts = getAllProducts();
// $allRooms = getAllRooms();
?>

<body class="p-3">
<div class="container-fluid">
  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4 bg-dark">
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
  <div class="border p-3 mb-3">
    
    <div id="cart-items" class="mb-3"></div>

    <div class="mb-2">
      <label for="notes" class="form-label">Notes</label>
      <textarea id="notes" class="form-control" rows="2" placeholder="e.g. Extra sugar..."></textarea>
    </div>

    <div class="mb-3 fw-bold" id="cart-total">EGP 0</div>

    <button class="btn btn-primary w-100">Confirm</button>
  </div>
</div>


    <div class="col-md-8">
      <h5>Latest Order</h5>
      <div class="d-flex gap-3 mb-3">
        <div class="card text-center" style="width: 6rem;">
          <div class="card-body p-2">
            <h6 class="card-title mb-0">Tea</h6>
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
          <button 
            class="btn btn-outline-primary btn-sm add" 
            data-id="<?= $product['id'] ?>" 
            data-name="<?= $product['product_name'] ?>" 
            data-price="<?= $product['price'] ?>"
          >
            <i class="fa-solid fa-cart-shopping"></i>
          </button>
        </div>
      </div>
    </div>
  <?php endwhile; ?>
</div>


    
    
  </div>
</div>
<script>
  const cart = [];

  document.addEventListener('DOMContentLoaded', () => {
    const addButtons = document.querySelectorAll('.add');

    addButtons.forEach(button => {
      button.addEventListener('click', () => {
        const id = button.getAttribute('data-id');
        const name = button.getAttribute('data-name');
        const price = parseFloat(button.getAttribute('data-price'));
        const quantity=0;
        if(cart.include(id)){
            quantity++;
        }

        const product = { id, name, price ,quantity};
        cart.push(product);
        console.log('Cart:', cart);
      });
    });
  });
  
  function updateCartUI() {
    const cartItemsContainer = document.getElementById('cart-items');
    const cartTotalDisplay = document.getElementById('cart-total');

    cartItemsContainer.innerHTML = '';
    let total = 0;

    cart.forEach(item => {
      total += item.price * item.quantity;

      const itemDiv = document.createElement('div');
      itemDiv.classList.add('d-flex', 'justify-content-between', 'mb-2');

      itemDiv.innerHTML = `
        <div>${item.name}</div>
        <div>
          <button class="btn btn-sm btn-secondary decrease" data-id="${item.id}">-</button>
          <span class="mx-2">${item.quantity}</span>
          <button class="btn btn-sm btn-secondary increase" data-id="${item.id}">+</button>
        </div>
        <div>EGP ${item.price * item.quantity}</div>
      `;

      cartItemsContainer.appendChild(itemDiv);
    });

    cartTotalDisplay.textContent = `EGP ${total}`;
  }

  function findCartItem(id) {
    return cart.find(item => item.id === id);
  }

  document.addEventListener('DOMContentLoaded', () => {
    const addButtons = document.querySelectorAll('.add');

    addButtons.forEach(button => {
      button.addEventListener('click', () => {
        const id = button.getAttribute('data-id');
        const name = button.getAttribute('data-name');
        const price = parseFloat(button.getAttribute('data-price'));

        let item = findCartItem(id);

        if (item) {
          item.quantity += 1;
        } else {
          cart.push({ id, name, price, quantity: 1 });
        }

        updateCartUI();
      });
    });

    document.getElementById('cart-items').addEventListener('click', (e) => {
      if (e.target.classList.contains('increase') || e.target.classList.contains('decrease')) {
        const id = e.target.getAttribute('data-id');
        const item = findCartItem(id);

        if (item) {
          if (e.target.classList.contains('increase')) {
            item.quantity += 1;
          } else if (e.target.classList.contains('decrease') && item.quantity > 1) {
            item.quantity -= 1;
          } else if (item.quantity === 1) {
            const index = cart.findIndex(i => i.id === id);
            cart.splice(index, 1);
          }

          updateCartUI();
        }
      }
    });
  });
</script>

</body>
</html>