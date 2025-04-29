<?php
// when working on funcational 
// don't forget DRY / Single responsibility principle

function getAllRooms() {
    include(__DIR__ . '/../connection.php');
    $sql = "SELECT * FROM rooms ORDER BY room_name ASC";
    $result = mysqli_query($myconnection, $sql);
    return $result;
}
function getRoomById($id) {
    include(__DIR__ . '/../connection.php');
    $sql = "SELECT room_name FROM rooms WHERE id = $id";
    $result = mysqli_query($myconnection, $sql);
    return $result;
}

?>

