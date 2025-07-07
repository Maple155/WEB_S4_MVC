<?php
require_once __DIR__ . '/../models/Demande.php';
require_once __DIR__ . '/../helpers/Utils.php';

class DemandeController {

    public static function getAllTypePrets() {
        $typePrets = Demande::getAllTypePrets();
        Flight::json($typePrets);
    }

    public static function getTypePretById($id) {
        $typePret = Demande::getTypePretById($id);
        Flight::json($typePret);
    }

    // // Créer une nouvelle demande de prêt
    // public static function createPret() {
    //     $data = Flight::request()->data;
    //     $id = Demande::createPret($data);
    //     $dateFormatted = Utils::formatDate(date('Y-m-d')); // Exemple d’utilisation de Utils
    //     Flight::json(['message' => 'Prêt créé', 'id' => $id]);
    // }

    public static function getCurrentClient() {
        $client = Demande::getCurrentClient();
        Flight::json($client);
    }
}
