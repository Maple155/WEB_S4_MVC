<?php
include('connection.php');
function findAllNote(){
    $query = "SELECT * FROM Note";
    $result = connect()->query($query);
    $list = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $list[] = $row;
        }
    }
    connect()->close();
    return $list; 
} 
?>