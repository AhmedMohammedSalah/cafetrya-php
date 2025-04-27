<?php
function getPendingOrders() {
    $sql = "SELECT * FROM orders WHERE status = 'pending'";
    include(__DIR__ . '/../connection.php');
    $result = mysqli_query($myconnection, $sql);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}
function getOrderItems($orderId) {
    $sql = "SELECT * FROM order_items WHERE order_id = $orderId";
    include(__DIR__ . '/../connection.php');
    $result = mysqli_query($myconnection, $sql);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}
function updateOrderStatus($orderId, $status) {
    $sql = "UPDATE orders SET status = '$status' WHERE id = $orderId";
    include(__DIR__ . '/../connection.php');
    return mysqli_query($myconnection, $sql);
}
function getPendingOrdersCount() {
    $sql = "SELECT COUNT(*) as count FROM orders WHERE status = 'pending'";
    include(__DIR__ . '/../connection.php');
    $result = mysqli_query($myconnection, $sql);
    if (!$result) {
        die("Query failed: " . mysqli_error($myconnection)); 
    }
    if (mysqli_num_rows($result) == 0) {
        return 0;
    }
    $row = mysqli_fetch_assoc($result);
    return $row['count'];
}

function getPendingOrdersPaginated($limit, $offset) {
    $sql = "SELECT * FROM orders WHERE status = 'pending' ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
    include(__DIR__ . '/../connection.php');
    $result = mysqli_query($myconnection, $sql);
    if (!$result) {
        die("Query failed: " . mysqli_error($myconnection)); 
    }
    if (mysqli_num_rows($result) == 0) {
        return [];
    }
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}
function addOrder($user_id,$roomId,$total,$status,$notes) {
    $sql = "INSERT INTO orders (user_id, room_id, total, status,notes) 
    VALUES ($user_id, $roomId, $total, 'pending','$notes')";
    include(__DIR__ . '/../connection.php');
    if (mysqli_query($myconnection, $sql)) {
        $order_id = mysqli_insert_id($myconnection);
        return $order_id;  
    } else {
        return false;  
    }  
}

function addOrderItems($order_id) {
  //  include(__DIR__ .'/../../models/product.php');
    include(__DIR__ . '/../connection.php');
    if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
        foreach ($_SESSION['cart'] as $item) {
            $product = getProductById((int)$item['id']);
            $product_id = $product['id'];
            $quantity = $item['quantity'];
            $price = $product['price'];
            $total_price = $price * $quantity;
            $sql = "INSERT INTO order_items (order_id, product_id, quantity) 
                               VALUES ($order_id, $product_id, $quantity)";
            
            if (!mysqli_query($myconnection, $sql)) {
                return false;  
            }
        }
        return true; 
    } else {
        return false;
    }
}



    
?>

