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

    public static function getInterestsByPeriod() {
        // Récupération des paramètres
        $params = Flight::request()->query;

        // Sécurisation des dates incomplètes (au format 'YYYY-MM')
        $date_debut = isset($params['date_debut']) 
            ? $params['date_debut'] . '-01' 
            : date('Y-m-01', strtotime('-1 year'));

        $date_fin = isset($params['date_fin']) 
            ? date('Y-m-t', strtotime($params['date_fin'] . '-31')) 
            : date('Y-m-t');

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
           Flight::halt(500, json_encode([
                'error' => 'Erreur de base de données',
                'details' => $e->getMessage()
            ]));
        }
    }
    public static function getAllSim() {
        $simulations = EF::getAllSim();
        Flight::json($simulations);
    }

    public static function compareSimulations(){
        $id1 = Flight::request()->query->id1;
        $id2 = Flight::request()->query->id2;

        if (!$id1 || !$id2 || $id1 === $id2) {
            Flight::json(['error' => 'Sélectionnez deux simulations différentes'], 400);
            return;
        }

        $sim1 = EF::getSimById($id1);
        $sim2 = EF::getSimById($id2);

        if (!$sim1 || !$sim2) {
            Flight::json(['error' => 'Simulation non trouvée'], 404);
            return;
        }

        Flight::json(['sim1' => $sim1, 'sim2' => $sim2]);
    }

}
