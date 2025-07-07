<?php
require_once __DIR__ . '/../controllers/DemandeController.php';

Flight::route('GET /type_prets', ['DemandeController', 'getAllTypePrets']);
Flight::route('GET /type_prets/@id', ['DemandeController', 'getTypePretById']);

Flight::route('POST /prets', ['DemandeController', 'createPret']);
Flight::route('GET /prets', ['DemandeController', 'getAllPrets']);
Flight::route('GET /prets/@id', ['DemandeController', 'generatePDF']);

Flight::route('GET /currentClient', ['DemandeController', 'getCurrentClient']);
Flight::route('GET /allClients', ['DemandeController', 'getAllClient']);
