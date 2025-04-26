<?php
    $servername="localhost:3306";
    $username="root";
    $password="116102";
    $dbname ="mydb";
    $myconnection = mysqli_connect($servername,$username,$password,$dbname);

    if (!$myconnection) {
        die("Connection failed: " . mysqli_connect_error());
    }
?>