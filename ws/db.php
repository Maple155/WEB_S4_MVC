<?php
function getDB() {
    // $host = 'localhost';
    // $dbname = 'tp_flight';
    // $username = 'root';
    // $password = 'root';

    $host = 'localhost';
    $dbname = 'db_s2_ETU003113';
    $username = 'ETU003113';
    $password = 'q3fZmo1u';

    try {
        return new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    } catch (PDOException $e) {
        die(json_encode(['error' => $e->getMessage()]));
    }
}
