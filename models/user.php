<?php

function getUserById($userId) {
    $sql = "SELECT * FROM users WHERE id = $userId";
    include(__DIR__ . '/../connection.php');
    $result = mysqli_query($myconnection, $sql);
    return mysqli_fetch_assoc($result);
}
function checkMail ($email) {
    $sql = "SELECT * FROM users WHERE email = '$email'";
    include(__DIR__ . '/../connection.php');
    if (mysqli_query($myconnection, $sql)) {
        return true;
    } else {
        return false;
    }
}
function addUser($name, $password, $email,$image,$age , $room_id) {
    $sql = "INSERT INTO users (name, password, email, image, age, room_id) VALUES ('$name', '$password', '$email', '$image', $age, $room_id)";
    include(__DIR__ . '/../connection.php');
    if (mysqli_query($myconnection, $sql)) {
        return true;
    } else {
        return false;
    }
}

?>