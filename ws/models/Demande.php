<?php
require_once __DIR__ . '/../db.php';

class Demande {
    public static function getAllTypePrets() {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM type_pret");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getTypePretById($id) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM type_pret WHERE id_type_pret = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function createPret($data) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO pret (id_client, id_type_pret, montant, date_demande) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$data->id_client, $data->id_type_pret, $data->montant]);
        return $db->lastInsertId();
    }

    public static function getCurrentClient() {
        $client = 1; 
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM client WHERE id_client = ?");
        $stmt->execute([$client]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
