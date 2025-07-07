<?php
require_once __DIR__ . '/../db.php';

class TypePret {
    public static function getAll() {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM type_pret");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM type_pret WHERE id_type_pret = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO type_pret (nom, taux_interet, duree_max_mois, montant_min, montant_max,age_min) VALUES (?, ?, ?, ?,?,?)");
        $stmt->execute([$data->nom, $data->taux_interet, $data->duree_max_mois, $data->montant_min,$data->montant_max,$data->age_min]);
        return $db->lastInsertId();
    }

    public static function update($id, $data) {
        $db = getDB();
        $stmt = $db->prepare("UPDATE type_pret SET nom = ?, taux_interet = ?, duree_max_mois = ?, montant_min = ?,montant_max = ?, age_min = ? WHERE id_type_pret = ?");
        $stmt->execute([$data->nom, $data->taux_interet, $data->duree_max_mois, $data->montant_min,$data->montant_max,$data->age_min, $id]);
    }

    public static function delete($id) {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM type_pret WHERE id_type_pret = ?");
        $stmt->execute([$id]);
    }
}
