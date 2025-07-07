<?php
require_once __DIR__ . '/../models/Demande.php';
require_once __DIR__ . '/../helpers/Utils.php';

class DemandeController {

    public static function getAllPrets() {
        $prets = Demande::getAllPrets();
        Flight::json($prets);
    }

    public static function getAllTypePrets() {
        $typePrets = Demande::getAllTypePrets();
        Flight::json($typePrets);
    }

    public static function getTypePretById($id) {
        $typePret = Demande::getTypePretById($id);
        Flight::json($typePret);
    }

    public static function createPret() {
        $data = Flight::request()->data;
        $message = Demande::createPret($data);
        Flight::json(['message' => $message['message']]);
    }

    public static function getCurrentClient() {
        $client = Demande::getCurrentClient();
        Flight::json($client);
    }

    public static function getAllClient() {
        $clients = Demande::getAllClient();
        Flight::json($clients);
    }
}
