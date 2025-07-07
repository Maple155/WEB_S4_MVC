<?php
function connect(){
    $host = "localhost";  
    $user = "root";       
    $pass = "";           
    $dbname = "Note";  
    
    $conn = new mysqli($host, $user, $pass, $dbname);
    
    if ($conn->connect_error) {
        die("Connexion échouée : " . $conn->connect_error);
    }
    return $conn;
}
?>