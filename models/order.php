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


?>