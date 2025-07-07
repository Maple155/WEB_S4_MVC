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
        $date = Utils::formatDate($data->date);
        $stmt->execute([$data->montant, $date]);
        return $db->lastInsertId();
    }
    public static function getMonthlyInterests($date_debut, $date_fin) {
        $db = getDB();
        
        $query = "SELECT 
                    m.annee,
                    m.mois,
                    CONCAT(m.annee, '-', LPAD(m.mois, 2, '0')) AS periode,
                    COUNT(DISTINCT m.id_pret) AS nombre_prets,
                    SUM(m.capital) AS capital_total,
                    ROUND(SUM(m.interet), 2) AS interets_mensuels
                FROM mensualite m
                JOIN pret p ON m.id_pret = p.id_pret
                WHERE DATE(CONCAT(m.annee, '-', LPAD(m.mois, 2, '0'), '-01')) BETWEEN :date_debut AND :date_fin
                GROUP BY m.annee, m.mois
                ORDER BY m.annee, m.mois";

        // Logging SQL avec valeurs injectées
        $queryForLog = str_replace(
            [':date_debut', ':date_fin'],
            ["'" . addslashes($date_debut) . "'", "'" . addslashes($date_fin) . "'"],
            $query
        );

        $logLine = "[" . date('Y-m-d H:i:s') . "] " . $queryForLog . PHP_EOL;
        file_put_contents(__DIR__ . '/../sql.log', $logLine, FILE_APPEND);

        // Exécution de la requête
        $stmt = $db->prepare($query);
        $stmt->execute([
            ':date_debut' => $date_debut,
            ':date_fin' => $date_fin
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }




}
