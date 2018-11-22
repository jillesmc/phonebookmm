<?php
$routes = require_once __DIR__ . "/../app/routes.php";
$session = new \Core\Auth();
$route = new \Core\Route($routes);
