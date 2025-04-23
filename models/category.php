<?php 
    function addCategory($categoryName) {
        include_once( '../connection.php');
        $sql = "INSERT INTO categories (categoryName) VALUES ('$categoryName')";
        if (mysqli_query($myconnection, $sql)) {
            return true;
        } else {
            return false;
        }
    }
    // get all categories
    // return array of categories
    function getCategories() {
        include_once ('../connection.php');
        $sql = "SELECT * FROM categories";
        $result = mysqli_query($myconnection, $sql);
        $categories = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $categories[] = $row;
        }
        return $categories;
    }
?>