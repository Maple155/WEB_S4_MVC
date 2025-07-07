<?php
require_once __DIR__ . '/../models/Etudiant.php';
require_once __DIR__ . '/../helpers/Utils.php';
require_once __DIR__ . '/../models/EF.php';



class AdminController {
    public static function getAll() {
        $etudiants = Etudiant::getAll();
        Flight::json($etudiants);
    }
    public static function login(){
        $data = Flight::request()->data;
        $db = getDB();
        $stmt = $db->prepare("SELECT id_etablissement, nom FROM etablissement_financiere WHERE nom = ? AND mdp = ?");
        $stmt->execute([$data->nom, $data->mdp]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        if($admin){
            Flight::json($admin);
        }
    }
    public static function addFondEF(){
        $data = Flight::request()->data;
        $id = EF::addFond($data);
        Flight::json(['message' => 'Fond ajouté', 'id' => $id]);
    }

    public function getInterestsByPeriod() {
        // Récupération des paramètres
        $params = Flight::request()->query;
        $date_debut = $params['date_debut'] ?? date('Y-m-01', strtotime('-1 year'));
        $date_fin = $params['date_fin'] ?? date('Y-m-t');

        // Validation des dates
        if (!strtotime($date_debut) || !strtotime($date_fin)) {
            Flight::halt(400, json_encode(['error' => 'Format de date invalide']));
            return;
        }

        try {
            // Appel au modèle
            $details = EF::getMonthlyInterests($date_debut, $date_fin);
            
            // Calcul du total
            $total_interets = array_sum(array_column($details, 'interets_mensuels'));

            // Réponse structurée
            Flight::json([
                'periode_debut' => $date_debut,
                'periode_fin' => $date_fin,
                'total_interets' => $total_interets,
                'details' => $details
            ]);
        } catch (PDOException $e) {
            Flight::halt(500, json_encode(['error' => 'Erreur de base de données']));
        }
    }
}
