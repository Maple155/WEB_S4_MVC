<?php
require_once __DIR__ . '/../db.php';

class Mensualite {
    public static function getAll() {
        $db = getDB();
        $stmt = $db->query("SELECT 
            annee,
            mois,
            SUM(capital + interet + assurance) AS mensualite_totale
        FROM 
            mensualite
        GROUP BY 
            annee, mois
        ORDER BY 
            annee, mois");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
