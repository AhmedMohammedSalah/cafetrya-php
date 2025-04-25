<!-- <?php

// // add product 
// function addProduct($productName, $image ,$price, $categoryId) {
//     include_once(__DIR__ . '/../connection.php');
//     $sql = "INSERT INTO products (productName, image, price, categoryId) VALUES ('$productName', '$image', $price, $categoryId)";
//     if (mysqli_query($myconnection, $sql)) {
//         return true;
//     } else {
//         return false;
//     }
    
// }

?>
<?php 

// add product 
function addProduct($productName, $image ,$price, $categoryId) {
    include_once(__DIR__ . '/../connection.php');
    $sql = "INSERT INTO products (product_name, image, price, category_Id) VALUES ('$productName', '$image', $price, $categoryId)";
    if (mysqli_query($myconnection, $sql)) {
        return true;
    } else {
        return false;
    }
    
}
function getAllProducts() {
    include_once(__DIR__ . '/../connection.php');
    $sql = "select * from products";
    $allProducts = mysqli_query($myconnection, $sql);

    if (!$allProducts) {
        die("Query failed: " . mysqli_error($myconnection));
    }

    return $allProducts;
}

?>