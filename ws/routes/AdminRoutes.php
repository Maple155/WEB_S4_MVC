<?php
require_once __DIR__ . '/../controllers/AdminController.php';

Flight::route('POST /admin/login', [`AdminController`, 'login']);
?>