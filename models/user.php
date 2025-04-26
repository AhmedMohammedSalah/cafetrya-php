<?php

function getUserById($userId) {
    $sql = "SELECT * FROM users WHERE id = $userId";
    include(__DIR__ . '/../connection.php');
    $result = mysqli_query($myconnection, $sql);
    return mysqli_fetch_assoc($result);
}
?>