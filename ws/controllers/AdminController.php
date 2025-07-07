<?php
require_once __DIR__ . '/../models/Etudiant.php';
require_once __DIR__ . '/../helpers/Utils.php';



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
    }

}
