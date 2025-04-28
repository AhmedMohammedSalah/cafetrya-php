<?php
    include_once('models/category.php');
    $categories = getCategories();
    print_r($categories);    
?>