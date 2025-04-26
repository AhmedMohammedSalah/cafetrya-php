<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .accordion-button:not(.collapsed) {
            background-color: #f8f9fa;
            color: #000;
        }
        .order-header {
            display: flex;
            justify-content: space-between;
            width: 100%;
        }
        .order-id {
            font-weight: bold;
        }
        .order-date {
            color: #6c757d;
        }
        .order-total {
            font-weight: bold;
            color: #dc3545;
        }
        .product-img {
            max-width: 80px;
            max-height: 80px;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h1 class="mb-4">Pending Orders</h1>
        
        <?php
        // Include necessary files
        include_once(__DIR__ . '/../../connection.php');
        include_once(__DIR__ . '/../../models/product.php');
        include_once(__DIR__ . '/../../models/order.php');
        
        // Get pending orders
        $orders = getPendingOrders();
        
        if (empty($orders)): ?>
            <div class="alert alert-info">No pending orders found.</div>
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
                                    <span class="order-id">Order #<?= $order['id'] ?? 'N/A' ?></span>
                                    <span class="order-date"><?= isset($order['created_at']) ? date('M d, Y H:i', strtotime($order['created_at'])) : 'Date N/A' ?></span>
                                    <span class="customer"><?= htmlspecialchars($customer['name'] ?? 'Customer N/A') ?></span>
                                    <span class="order-total">$<?= isset($order['total']) ? number_format($order['total'], 2) : '0.00' ?></span>
                                </div>
                            </button>
                        </h2>
                        <div id="collapse<?= $order['id'] ?? '' ?>" class="accordion-collapse collapse" 
                             aria-labelledby="heading<?= $order['id'] ?? '' ?>" data-bs-parent="#ordersAccordion">
                            <div class="accordion-body">
                                <div class="row mb-3">
                                    
                                    <div class="col-md-6">
                                        <h5>Customer Information</h5>
                                        <p><strong>Name:</strong><?php echo $customer["name"] ;?> </p>
                                        <p><strong>Email:</strong><?php echo $customer["email"] ;?> </p>
                                      
                                    </div>
                                    <div class="col-md-6">
                                        <h5>Order Information</h5>
                                        <p><strong>Status:</strong> 
                                            <span class="badge bg-warning text-dark"><?= isset($order['status']) ? ucfirst($order['status']) : 'N/A' ?></span>
                                        </p>
                                        <p><strong>Date:</strong> <?= isset($order['created_at']) ? date('F j, Y g:i a', strtotime($order['created_at'])) : 'N/A' ?></p>
                                        <p class="bg-success fw-bold text-light"><strong >notes:</strong> <?= $order['notes'] ?? 'N/A' ?></p>
                                    </div>
                                </div>
                                
                                <?php if (!empty($orderItems)): ?>
                                <h5 class="mt-4">Order Items</h5>
                                <div class="table-responsive">
                                    <table class="table table-striped">
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
                                                            
                                                            <img src="<?php echo $product['image'];?>" 
                                                                 alt="<?php echo $product['product_name'] ?? ''; ?>" 
                                                                 class="product-img img-thumbnail">
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>$<?= isset($product['price']) ? number_format($product['price'], 2) : '0.00' ?></td>
                                                    <td><?= $item['quantity'] ?? 0 ?></td>
                                                    <td>$<?= number_format($subtotal, 2) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                            <tr class="table-secondary">
                                                <td colspan="4" class="text-end"><strong>Total:</strong></td>
                                                <td><strong>$<?= number_format($orderTotal, 2) ?></strong></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <?php else: ?>
                                    <div class="alert alert-warning">No items found for this order.</div>
                                <?php endif; ?>
                                
                                <div class="d-flex justify-content-end mt-3">
                                    <form  method="POST">
                                        <input type="hidden" name="order_id" value="<?= $order['id'] ?? '' ?>">
                                        <button type="submit" name="completed" class="btn btn-success me-2">Mark as Completed</button>
                                    </form>
                                    <form method="POST">
                                        <input type="hidden" name="order_id" value="<?= $order['id'] ?? '' ?>">
                                        <button type="submit" name="cancelled" class="btn btn-danger">Cancel Order</button>
                                    </form>
                                    <?php
                                    if (isset($_POST['completed'])) {
                                        $orderId = $_POST['order_id'];
                                        updateOrderStatus($orderId, 'delivered');
                                        header("Location: " . $_SERVER['PHP_SELF']);
                                        exit();
                                    }
                                    if (isset($_POST['cancelled'])) {
                                        $orderId = $_POST['order_id'];
                                        updateOrderStatus($orderId, 'cancelled');
                                        header("Location: " . $_SERVER['PHP_SELF']);
                                        exit();
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>