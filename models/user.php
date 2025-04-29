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
    $result = mysqli_query($myconnection, $sql);
    $isexsist =mysqli_fetch_assoc($result);
    if ($isexsist) {
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
function getAllUsers() {
    $sql = "SELECT * FROM users ORDER BY name";
    include(__DIR__ . '/../connection.php');
    $result = mysqli_query($myconnection, $sql);
    return $result;
}
function deleteUser($userId) {
    include(__DIR__ . '/../connection.php');
    $sql = "DELETE FROM users WHERE id = $userId";
    if (mysqli_query($myconnection, $sql)) {
        return true;
    } else {
        return false;
    }
}
function updateUser($userId, $name,$email,$password ,$image,$age, $room_id) {
    include(__DIR__ . '/../connection.php');
    $sql = "UPDATE users SET name = '$name', email='$email', image = '$image', age = $age, room_id = $room_id, password='$password' WHERE id = $userId";
    if (mysqli_query($myconnection, $sql)) {
        return true;
    } else {
        return false;
    }
}

?>