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
?>