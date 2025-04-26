<?php
function getAllRooms() {
    include_once(__DIR__ . '/../connection.php');

    $sql = "SELECT * FROM rooms ORDER BY room_name ASC";
    $result = mysqli_query($myconnection, $sql);

    if (!$result) {
        die("Query failed: " . mysqli_error($myconnection)); 
    }

    if (mysqli_num_rows($result) == 0) {
        echo "No rooms found!";
    }
    return $result;
}

?>

