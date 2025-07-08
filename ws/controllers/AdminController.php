<?php
require_once __DIR__ . '/../models/Etudiant.php';
require_once __DIR__ . '/../helpers/Utils.php';
require_once __DIR__ . '/../models/EF.php';
require_once __DIR__ . '/../models/Mensualite.php';



class AdminController
{
    public static function getMontantDisponible() {
    $mensualites = Mensualite::getAll();
    $argentParMois = EF::getArgentParMois();
    
    $mensualitesIndex = [];
    foreach ($mensualites as $mensualite) {
        $key = $mensualite['annee'] . '-' . str_pad($mensualite['mois'], 2, '0', STR_PAD_LEFT);
        $mensualitesIndex[$key] = $mensualite['mensualite_totale'];
    }
    
    $argentIndex = [];
    foreach ($argentParMois as $argent) {
        $key = $argent['annee'] . '-' . str_pad($argent['mois'], 2, '0', STR_PAD_LEFT);
        $argentIndex[$key] = $argent['solde_cumulatif'];
    }
    
    $allDates = array_merge(array_keys($mensualitesIndex), array_keys($argentIndex));
    if (empty($allDates)) {
        header('Content-Type: application/json');
        echo json_encode([]);
        return;
    }
    
    sort($allDates);
    $dateMin = $allDates[0];
    $dateMax = end($allDates);
    
    list($anneeMin, $moisMin) = explode('-', $dateMin);
    list($anneeMax, $moisMax) = explode('-', $dateMax);
    
    $result = [];
    $soldeCumulatifPrecedent = 0;
    
    for ($annee = $anneeMin; $annee <= $anneeMax; $annee++) {
        $moisDebut = ($annee == $anneeMin) ? $moisMin : 1;
        $moisFin = ($annee == $anneeMax) ? $moisMax : 12;
        
        for ($mois = $moisDebut; $mois <= $moisFin; $mois++) {
            $key = $annee . '-' . str_pad($mois, 2, '0', STR_PAD_LEFT);
            
            $mensualiteTotale = isset($mensualitesIndex[$key]) ? $mensualitesIndex[$key] : 0;
            $soldeCumulatif = isset($argentIndex[$key]) ? $argentIndex[$key] : $soldeCumulatifPrecedent;
            
            $montantDisponible = $soldeCumulatif + $mensualiteTotale;
            
            $result[] = [
                'annee' => (int)$annee,
                'mois' => (int)$mois,
                'montant_disponible' => $montantDisponible
            ];
            
            if (isset($argentIndex[$key])) {
                $soldeCumulatifPrecedent = $soldeCumulatif;
            }
        }
    }
    
    Flight::json($result);
}
    public static function login()
    {
        $data = Flight::request()->data;
        $db = getDB();
        $stmt = $db->prepare("SELECT id_etablissement, nom FROM etablissement_financiere WHERE nom = ? AND mdp = ?");
        $stmt->execute([$data->nom, $data->mdp]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($admin) {
            Flight::json($admin);
        }
    }
    public static function addFondEF()
    {
        $data = Flight::request()->data;
        $id = EF::addFond($data);
        Flight::json(['message' => 'Fond ajouté', 'id' => $id]);
    }

    public static function getInterestsByPeriod()
    {
        $params = Flight::request()->query;

        $date_debut = isset($params['date_debut'])
            ? $params['date_debut'] . '-01'
            : date('Y-m-01', strtotime('-1 year'));

        $date_fin = isset($params['date_fin'])
            ? date('Y-m-t', strtotime($params['date_fin'] . '-31'))
            : date('Y-m-t');

        if (!strtotime($date_debut) || !strtotime($date_fin)) {
            Flight::halt(400, json_encode(['error' => 'Format de date invalide']));
            return;
        }

        try {
            $details = EF::getMonthlyInterests($date_debut, $date_fin);

            $total_interets = array_sum(array_column($details, 'interets_mensuels'));

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

}
