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
                    YEAR(p.date_debut) AS annee,
                    MONTH(p.date_debut) AS mois,
                    DATE_FORMAT(p.date_debut, '%Y-%m') AS periode,
                    COUNT(p.id_pret) AS nombre_prets,
                    SUM(p.montant) AS capital_total,
                    ROUND(SUM(p.montant * (tp.taux_interet/100) / 12), 2) AS interets_mensuels
                FROM pret p
                JOIN type_pret tp ON p.id_type_pret = tp.id_type_pret
                WHERE p.date_debut BETWEEN :date_debut AND :date_fin
                GROUP BY YEAR(p.date_debut), MONTH(p.date_debut)
                ORDER BY annee, mois";

        // Construire la requête complète avec les paramètres injectés (pour logging)
        $queryForLog = str_replace(
            [':date_debut', ':date_fin'],
            ["'" . addslashes($date_debut) . "'", "'" . addslashes($date_fin) . "'"],
            $query
        );

        // Construire la ligne de log avec date + requête
        $logLine = "[" . date('Y-m-d H:i:s') . "] " . $queryForLog . PHP_EOL;

        // Écrire dans un fichier local (créera le fichier s'il n'existe pas, et ajoute à la fin)
        file_put_contents(__DIR__ . '/../sql.log', $logLine, FILE_APPEND);

        // Préparation et exécution
        $stmt = $db->prepare($query);
        $stmt->execute([
            ':date_debut' => $date_debut,
            ':date_fin' => $date_fin
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



}
