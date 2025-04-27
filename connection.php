<?php
    $servername="localhost";
    $username="root";
    $password="";
    $dbname ="mydb";
    $myconnection = mysqli_connect($servername,$username,$password,$dbname);

    if (!$myconnection) {
        die("Connection failed: " . mysqli_connect_error());
    }
?>