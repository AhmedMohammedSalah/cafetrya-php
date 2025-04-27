<?php
// when working on funcational 
// don't forget DRY / Single responsibility principle

function getAllRooms() {
    include(__DIR__ . '/../connection.php');
    $sql = "SELECT * FROM rooms ORDER BY room_name ASC";
    $result = mysqli_query($myconnection, $sql);
    return $result;
}

?>

