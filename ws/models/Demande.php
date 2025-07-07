    <?php
    require_once __DIR__ . '/../db.php';
    // ini_set('display_errors', 1);
    // error_reporting(E_ALL);

    class Demande {
        public static function getAllPrets() {
            $db = getDB();
            $stmt = $db->query("SELECT p.*, tp.nom AS nom_type_pret, tp.id_type_pret, c.* FROM pret p JOIN type_pret tp on tp.id_type_pret=p.id_type_pret JOIN client c on c.id_client=p.id_client");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        public static function getAllTypePrets() {
            $db = getDB();
            $stmt = $db->query("SELECT * FROM type_pret");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public static function getTypePretById($id) {
            $db = getDB();
            $stmt = $db->prepare("SELECT * FROM type_pret WHERE id_type_pret = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        public static function getCurrentClient($id_client) {
            $client = $id_client; 
            $db = getDB();
            $stmt = $db->prepare("SELECT * FROM client WHERE id_client = ?");
            $stmt->execute([$client]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        public static function getAllClient() {
            $db = getDB();
            $stmt = $db->query("SELECT * FROM client");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public static function getArgentActuel() {
            $db = getDB();  
            $stmt = $db->query("SELECT SUM(montant) AS argent_actuel FROM mouvement_argent");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['argent_actuel'] !== null ? $result['argent_actuel'] : 0;
        }

        private static function validatePretData($data) {
            if (empty($data->id_type_pret) || empty($data->montant) ||
                empty($data->mois_max) || empty($data->id_client)) {
                return 'Données incomplètes reçues';
            }
            return true;
        }
        
        private static function validatePretRules($data, $typePret, $client) {
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
        
        private static function insertMensualites($db, $data, $typePret, $id_pret) {
            $taux_annuel = $typePret['taux_interet'] / 100;
            $taux_mensuel = $taux_annuel / 12;
            $annuite = ($data->montant * $taux_mensuel) / (1 - pow(1 + $taux_mensuel, -$data->mois_max));
            $assurance_mensuelle = ($data->montant * ($data->assurance / 100)) / 12;
        
            $capital_restant = $data->montant;
            $mois_debut = (int) date('m');
            $annee_debut = (int) date('Y');
        
            for ($i = 1; $i <= $data->mois_max; $i++) {
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

        public static function createPret($data) {
            $db = getDB();
        
            $validation = self::validatePretData($data);
            if ($validation !== true) return ['message' => $validation];
        
            $typePret = self::getTypePretById($data->id_type_pret);
            if (!$typePret) return ['message' => 'Type de prêt invalide'];
        
            $client = self::getCurrentClient($data->id_client);
            if (!$client || empty($client['date_de_naissance'])) {
                return ['message' => 'Client introuvable ou date de naissance manquante'];
            }
        
            // $validation = self::validatePretRules($data, $typePret, $client);
            // if ($validation !== true) return ['message' => $validation];
        
            try {
                $stmt = $db->prepare("
                    INSERT INTO pret (montant, date_debut, duree_mois, assurance, delai_mois, id_type_pret, id_client)
                    VALUES (?, NOW(), ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $data->montant, $data->mois_max, $data->assurance, $data->delai,
                    $data->id_type_pret, $data->id_client
                ]);
                $id_pret = $db->lastInsertId();
        
                self::insertMensualites($db, $data, $typePret, $id_pret);
        
            } catch (PDOException $e) {
                return ['message' => "Erreur SQL : " . $e->getMessage()];
            }
        
            return ['message' => 'Prêt accepté avec mensualités générées avec succès'];
        }
        
         
    }
