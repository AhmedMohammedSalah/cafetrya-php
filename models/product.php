<?php
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
function getAllAvaliableProducts() {
    include(__DIR__ . '/../connection.php');
    $sql =  "SELECT * FROM products  WHERE availability=1  ORDER BY product_name ASC";
    $result = mysqli_query($myconnection, $sql);
    return $result;
}

function editProduct($productId, $productName, $image, $price, $categoryId) {
    include(__DIR__ . '/../connection.php');
    $sql = "UPDATE products SET product_name = '$productName', image = '$image', price = $price, category_id = $categoryId WHERE id = $productId";
    if (mysqli_query($myconnection, $sql)) {
        return true;
    } else {
        return false;
    }
}
function deleteProduct($productId) {
    include(__DIR__ . '/../connection.php');
    $sql = "DELETE FROM products WHERE id = $productId";
    if (mysqli_query($myconnection, $sql)) {
        return true;
    } else {
        return false;
    }
}
function changeAvailability($productId) {
    include(__DIR__ . '/../connection.php');
    $oldAvailability = getProductById($productId)['availability'];
    $newAvailability = $oldAvailability == 1 ? 0 : 1;
    $sql = "UPDATE products SET availability = $newAvailability WHERE id = $productId";
    if (mysqli_query($myconnection, $sql)) {
        return true;
    } else {
        return false;
    }
}
function getProductsByCategory($categoryId) {
    include(__DIR__ . '/../connection.php');
    $sql = "SELECT * FROM products WHERE category_id = $categoryId";
    $result = mysqli_query($myconnection, $sql);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

?>