<?php
require_once __DIR__ . '/../controllers/AdminController.php';

Flight::route('POST /admin/login', ['AdminController', 'login']);
Flight::route('POST /admin/addFond', ['AdminController',  'addFondEF']);
Flight::route('GET /admin/interets',['AdminController', 'getInterestsByPeriod']); 
Flight::route('GET /admin/fonds',['AdminController', 'getMontantDisponible']); 
?>