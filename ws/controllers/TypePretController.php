<?php
require_once __DIR__ . '/../models/TypePret.php';
require_once __DIR__ . '/../helpers/Utils.php';

class TypePretController {
    public static function getAll() {
        $etudiants = TypePret::getAll();
        Flight::json($etudiants);
    }

    public static function getById($id) {
        $etudiant = TypePret::getById($id);
        Flight::json($etudiant);
    }

    public static function create() {
        $data = Flight::request()->data;
        $id = TypePret::create($data);
        Flight::json(['message' => 'Pret ajouté', 'id' => $id]);
    }

    public static function update($id) {
        parse_str(file_get_contents('php://input'), $data);
        TypePret::update($id, $data);
        Flight::json(['message' => 'Pret modifié']);
    }

    public static function delete($id) {
        TypePret::delete($id);
        Flight::json(['message' => 'Pret supprimé']);
    }
}
