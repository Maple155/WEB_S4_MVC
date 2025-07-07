<?php
require_once __DIR__ . '/../controllers/TypePretController.php';

Flight::route('GET /type_prets', ['TypePretController', 'getAll']);
Flight::route('GET /type_prets/@id', ['TypePretController', 'getById']);
Flight::route('POST /type_prets', ['TypePretController', 'create']);
Flight::route('PUT /type_prets/@id', ['TypePretController', 'update']);
Flight::route('DELETE /type_prets/@id', ['TypePretController', 'delete']);
