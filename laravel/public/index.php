<?php

// Bootstrap the Core PHP OOP application environment
require_once __DIR__ . '/../src/bootstrap.php';

// Load and execute route mapping
$router = require_once __DIR__ . '/../src/routes.php';
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
