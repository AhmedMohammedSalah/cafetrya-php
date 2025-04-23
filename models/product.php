<?php

// add product 
function addProduct($productName, $image ,$price, $categoryId) {
    include_once(__DIR__ . '/../connection.php');
    $sql = "INSERT INTO products (productName, image, price, categoryId) VALUES ('$productName', '$image', $price, $categoryId)";
    if (mysqli_query($myconnection, $sql)) {
        return true;
    } else {
        return false;
    }
    
}

?>