<?php
require_once __DIR__ . '/../db.php';
// ini_set('display_errors', 1);
// error_reporting(E_ALL);

class Demande
{
    public static function getAllPrets()
    {
        $db = getDB();
        $stmt = $db->query("SELECT p.*, tp.nom AS nom_type_pret, tp.id_type_pret, c.* FROM pret p JOIN type_pret tp on tp.id_type_pret=p.id_type_pret JOIN client c on c.id_client=p.id_client");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function createMouvArgent($db, $montant, $date) 
    {
        $stmt = $db->prepare("
        INSERT INTO mouvement_argent (montant, date_)
        VALUES (?, ?)
            ");
        $stmt->execute([
            $montant,
            $date
        ]);
    }

    public static function findByIdPret($id)
    {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM pret where id_pret = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public static function getAllTypePrets()
    {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM type_pret");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getTypePretById($id)
    {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM type_pret WHERE id_type_pret = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getCurrentClient($id_client)
    {
        $client = $id_client;
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM client WHERE id_client = ?");
        $stmt->execute([$client]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getAllClient()
    {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM client");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getArgentActuel()
    {
        $db = getDB();
        $stmt = $db->query("SELECT SUM(montant) AS argent_actuel FROM mouvement_argent");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['argent_actuel'] !== null ? $result['argent_actuel'] : 0;
    }

    private static function validatePretData($data)
    {
        if (
            empty($data->id_type_pret) || empty($data->montant) ||
            empty($data->mois_max) || empty($data->id_client)
        ) {
            return 'Données incomplètes reçues';
        }
        return true;
    }

    private static function validatePretRules($data, $typePret, $client)
    {
        $dob = new DateTime($client['date_de_naissance']);
        $today = new DateTime();
        $age = $dob->diff($today)->y;

        $argentActuel = self::getArgentActuel();

        if ($typePret['montant_max'] < $data->montant) {
            return 'Le montant du prêt ne doit pas être supérieur à ' . $typePret['montant_max'];
        }
        if ($typePret['montant_min'] > $data->montant) {
            return 'Le montant du prêt ne doit pas être inférieur à ' . $typePret['montant_min'];
        }
        if ($age < $typePret['age_min']) {
            return "L'âge minimum pour un prêt est de {$typePret['age_min']} ans";
        }
        if ($data->mois_max > $typePret['duree_max_mois']) {
            return 'La durée maximale de remboursement est de ' . $typePret['duree_max_mois'] . ' mois';
        }
        if ($data->montant > $argentActuel) {
            return "L'argent disponible est insuffisant";
        }
        return true;
    }

    private static function insertMensualites($db, $data, $typePret, $id_pret)
    {
        $taux_annuel = $typePret['taux_interet'] / 100;
        $taux_mensuel = $taux_annuel / 12;
        $annuite = ($data->montant * $taux_mensuel) / (1 - pow(1 + $taux_mensuel, -$data->mois_max));
        $assurance_mensuelle = ($data->montant * ($data->assurance / 100)) / 12;

        $capital_restant = $data->montant;
        $datePret = $data->datePret; 

        $dateObj = new DateTime($datePret);
        $mois_debut = (int) $dateObj->format('m');
        $annee_debut = (int) $dateObj->format('Y');
        
        $duree = $data->mois_max - $data->delai;
        for ($i = 1; $i <= $duree; $i++) {
            $interet_mois = $capital_restant * $taux_mensuel;
            $capital_mois = $annuite - $interet_mois;

            $mois_echeance = $mois_debut + $i + $data->delai - 1;
            $annee_echeance = $annee_debut + floor(($mois_echeance - 1) / 12);
            $mois_echeance = (($mois_echeance - 1) % 12) + 1;

            $stmt = $db->prepare("
                    INSERT INTO mensualite (capital, interet, assurance, mois, annee, id_pret)
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
            $stmt->execute([
                round($capital_mois, 2),
                round($interet_mois, 2),
                round($assurance_mensuelle, 2),
                $mois_echeance,
                $annee_echeance,
                $id_pret
            ]);

            $capital_restant -= $capital_mois;
        }
    }

    public static function createPret($data)
    {
        $db = getDB();

        $validation = self::validatePretData($data);
        if ($validation !== true)
            return ['message' => $validation];

        $typePret = self::getTypePretById($data->id_type_pret);
        if (!$typePret)
            return ['message' => 'Type de prêt invalide'];

        $client = self::getCurrentClient($data->id_client);
        if (!$client || empty($client['date_de_naissance'])) {
            return ['message' => 'Client introuvable ou date de naissance manquante'];
        }

        // $validation = self::validatePretRules($data, $typePret, $client);
        // if ($validation !== true) return ['message' => $validation];

        try {
            $stmt = $db->prepare("
                    INSERT INTO pret (montant, date_debut, duree_mois, assurance, delai_mois, id_type_pret, id_client)
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
            $stmt->execute([
                $data->montant,
                $data->datePret,
                $data->mois_max,
                $data->assurance,
                $data->delai,
                $data->id_type_pret,
                $data->id_client
            ]);
            $id_pret = $db->lastInsertId();

            self::createMouvArgent($db, ($data->montant * -1), $data->datePret);
            self::insertMensualites($db, $data, $typePret, $id_pret);

        } catch (PDOException $e) {
            return ['message' => "Erreur SQL : " . $e->getMessage()];
        }

        return ['message' => 'Prêt accepté avec mensualités générées avec succès'];
    }

    public static function saveSimulation($data)
    {
        $db = getDB();

        $validation = self::validatePretData($data);
        if ($validation !== true)
            return ['message' => $validation];

        $typePret = self::getTypePretById($data->id_type_pret);
        if (!$typePret)
            return ['message' => 'Type de prêt invalide'];

        $client = self::getCurrentClient($data->id_client);
        if (!$client || empty($client['date_de_naissance'])) {
            return ['message' => 'Client introuvable ou date de naissance manquante'];
        }

        // $validation = self::validatePretRules($data, $typePret, $client);
        // if ($validation !== true) return ['message' => $validation];

        try {
            $stmt = $db->prepare("
                    INSERT INTO pret_simulation (montant, date_debut, duree_mois, assurance, delai_mois, id_type_pret, id_client)
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
            $stmt->execute([
                $data->montant,
                $data->datePret,
                $data->mois_max,
                $data->assurance,
                $data->delai,
                $data->id_type_pret,
                $data->id_client
            ]);
            $id_pret = $db->lastInsertId();

        } catch (PDOException $e) {
            return ['message' => "Erreur SQL : " . $e->getMessage()];
        }

        return ['message' => 'Simulation générées avec succès'];
    }

    public static function calculAnnuite($capital, $taux_annuel, $mois)
    {
        $taux_mensuel = $taux_annuel / 12 / 100;
        return $capital * $taux_mensuel / (1 - pow(1 + $taux_mensuel, -$mois));
    }

    public static function tableauAmortissement($capital, $taux_annuel, $mois, $assurance)
    {
        $mensualite = self::calculAnnuite($capital, $taux_annuel, $mois);
        $taux_mensuel = $taux_annuel / 12 / 100;

        $tableau = [];
        $capital_restant = $capital;

        for ($i = 1; $i <= $mois; $i++) {
            // Calcul des intérêts sur le capital restant
            $interet = $capital_restant * $taux_mensuel;

            // Le principal est la différence entre la mensualité et les intérêts
            $principal = $mensualite - $interet;

            // Assurance : généralement calculée sur le capital restant
            // Mais peut être fixe selon votre politique

            // Option 1: Assurance sur capital restant (recommandée)
            $assurance_mensuelle = $capital_restant * ($assurance / 100) / 12;

            // Option 2: Assurance fixe (décommentez si c'est votre cas)
            // $assurance_mensuelle = ($capital * $assurance / 100) / $mois;

            // Total à payer ce mois = mensualité + assurance
            $total = $mensualite + $assurance_mensuelle;

            $capital_restant -= $principal;

            $tableau[] = [
                'mois' => $i,
                'interet' => round($interet, 2),
                'principal' => round($principal, 2),
                'assurance' => round($assurance_mensuelle, 2),
                'total' => round($total, 2),
                'reste' => round(max($capital_restant, 0), 2)
            ];
        }

        return $tableau;
    }

    public static function verifierCalculs($capital, $taux_annuel, $mois, $assurance)
    {
        $mensualite = self::calculAnnuite($capital, $taux_annuel, $mois);
        $tableau = self::tableauAmortissement($capital, $taux_annuel, $mois, $assurance);

        $total_principal = array_sum(array_column($tableau, 'principal'));
        $total_interets = array_sum(array_column($tableau, 'interet'));
        $total_assurance = array_sum(array_column($tableau, 'assurance'));

        echo "=== VÉRIFICATION DES CALCULS ===\n";
        echo "Capital initial: " . number_format($capital, 2) . " Ar\n";
        echo "Mensualité (hors assurance): " . number_format($mensualite, 2) . " Ar\n";
        echo "Total principal remboursé: " . number_format($total_principal, 2) . " Ar\n";
        echo "Total intérêts payés: " . number_format($total_interets, 2) . " Ar\n";
        echo "Total assurance payée: " . number_format($total_assurance, 2) . " Ar\n";
        echo "Différence capital: " . number_format($capital - $total_principal, 2) . " Ar\n";

        if (abs($capital - $total_principal) < 1) {
            echo "✓ Calculs cohérents\n";
        } else {
            echo "✗ Erreur dans les calculs\n";
        }
    }

}
