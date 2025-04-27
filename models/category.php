<?php 
    function addCategory($categoryName) {
        include(__DIR__ . '/../connection.php');

        $sql = "INSERT INTO categories (category_name) VALUES ('$categoryName')";
        if (mysqli_query($myconnection, $sql)) {
            return true;
        } else {
            return false;
        }
    }
    // get all categories
    // return array of categories
    function getCategories() {
        include(__DIR__ . '/../connection.php');
        $sql = "SELECT * FROM categories";
        $result = mysqli_query($myconnection, $sql);
        $categories = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $categories[] = $row;
        }
        return $categories;
    }
    // get category by id
    function getCategoryById($categoryId) {
        include(__DIR__ . '/../connection.php');
        $sql = "SELECT * FROM categories WHERE id = $categoryId";
        $result = mysqli_query($myconnection, $sql);
        return mysqli_fetch_assoc($result);
    }
?>