    <?php
    require_once __DIR__ . '/../db.php';
    // ini_set('display_errors', 1);
    // error_reporting(E_ALL);

    class Demande {
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
    
        public static function createPret($data) {
            $db = getDB();
    
            if (
                empty($data->id_type_pret) || empty($data->montant) || 
                empty($data->mois_max) || empty($data->id_client)
            ) {
                return ['message' => 'Données incomplètes reçues'];
            }
    
            $typePret = self::getTypePretById($data->id_type_pret);
            if (!$typePret) {
                return ['message' => 'Type de prêt invalide'];
            }
    
            $client = self::getCurrentClient($data->id_client);
            if (!$client || empty($client['date_de_naissance'])) {
                return ['message' => 'Client introuvable ou date de naissance manquante'];
            }
    
            $dob = new DateTime($client['date_de_naissance']);
            $today = new DateTime();
            $age = $dob->diff($today)->y;
    
            $argentActuel = self::getArgentActuel();
    
            if ($typePret['montant_max'] < $data->montant) {
                return ['message' => 'Le montant du prêt ne doit pas être supérieur à ' . $typePret['montant_max']];
            } elseif ($typePret['montant_min'] > $data->montant) {
                return ['message' => 'Le montant du prêt ne doit pas être inférieur à ' . $typePret['montant_min']];
            } elseif ($age < $typePret['age_min']) {
                return ['message' => "L'âge minimum pour un prêt est de {$typePret['age_min']} ans"];
            } elseif ($data->mois_max > $typePret['duree_max_mois']) {
                return ['message' => 'La durée maximale de remboursement est de ' . $typePret['duree_max_mois'] . ' mois'];
            } elseif ($data->montant > $argentActuel) {
                return ['message' => "L'argent disponible est insuffisant"];
            }
    
            try {
                $stmt = $db->prepare("INSERT INTO pret (montant, date_debut, duree_mois, id_type_pret, id_client) VALUES (?, NOW(), ?, ?, ?)");
                $stmt->execute([$data->montant, $data->mois_max, $data->id_type_pret, $data->id_client]);
            } catch (PDOException $e) {
                return ['message' => "Erreur SQL : " . $e->getMessage()];
            }
    
            return ['message' => 'Prêt accepté avec succès'];
        }
    
        
    }
