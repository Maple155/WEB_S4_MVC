<?php
require_once __DIR__ . '/../db.php';

class EF {
    public static function getAll() {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM etablissement_financiere");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM etablissement_financiere WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public static function addFond($data){
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO mouvement_argent (montant, date_) VALUES (?, ?)");
        $stmt->execute([$data->montant, $data->date]);
        return $db->lastInsertId();
    }
}
