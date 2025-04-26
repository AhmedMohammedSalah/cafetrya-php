<?php

// add product 
function addProduct($productName, $image ,$price, $categoryId) {

    $sql = "INSERT INTO products (product_name, image, price, category_id) VALUES ('$productName', '$image', $price, $categoryId)";
    include(__DIR__ . '/../connection.php');
    if (mysqli_query($myconnection, $sql)) {
        return true;
    } else {
        return false;
    }
    
}
function getProductById($productId) {
    include(__DIR__ . '/../connection.php');
    $sql = "SELECT * FROM products WHERE id = $productId";
    $result = mysqli_query($myconnection, $sql);
    return mysqli_fetch_assoc($result);
}
function getAllProducts() {
    include(__DIR__ . '/../connection.php');
    $sql = "SELECT * FROM products ORDER BY product_name ASC";
    $result = mysqli_query($myconnection, $sql);
    return $result;
}

?>