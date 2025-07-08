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
        $simulations = EF::getAll();
        Flight::json($simulations);
    }

    public static function compareSimulations() {
            $id1 = $_GET['id1'];
            $id2 = $_GET['id2'];

            $conn = getDB();

            $s1 = EF::getSimById($id1);
            $s2 = EF::getSimById($id2);

            // On récupère les taux depuis type_pret
            $stmt = $conn->prepare("SELECT id_type_pret, taux_interet FROM type_pret");
            $stmt->execute();
            $types = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $typeMap = [];
            foreach ($types as $t) {
                $typeMap[$t['id_type_pret']] = $t['taux_interet'];
            }

            // Calculs
            function calculerSimulation($s, $taux_annuel) {
                $montant = $s['montant'];
                $duree = $s['duree_mois'];
                $assurance = $s['assurance'];
                $delai = $s['delai_mois'];

                $taux_mensuel = $taux_annuel / 12 / 100;
                $annuite = ($montant * $taux_mensuel) / (1 - pow(1 + $taux_mensuel, -$duree));
                $assurance_mensuelle = ($montant * ($assurance / 100)) / 12;
                $mensualite_totale = $annuite + $assurance_mensuelle;
                $interet_total = 0;
                $capitalRestant = $montant;

                for ($i = 1; $i <= $duree; $i++) {
                    $interetMois = $capitalRestant * $taux_mensuel;
                    $capitalMois = $annuite - $interetMois;
                    $interet_total += $interetMois;
                    $capitalRestant -= $capitalMois;
                }

                return [
                    "mensualite" => $mensualite_totale,
                    "interet_total" => $interet_total,
                    "taux_annuel" => $taux_annuel
                ];
            }

            $res1 = calculerSimulation($s1, $typeMap[$s1['id_type_pret']]);
            $res2 = calculerSimulation($s2, $typeMap[$s2['id_type_pret']]);

            echo json_encode([
                "sim1" => array_merge($s1, $res1),
                "sim2" => array_merge($s2, $res2)
            ]);
        }

}
