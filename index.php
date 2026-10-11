<?php

require_once __DIR__ . '/vendor/autoload.php';

use Src\Core\Routes;

$url = $_SERVER['REQUEST_URI'];

$rota = new Routes($url);

$rota->get('/', 'HomeController', 'home');
$rota->post('/', 'HomeController', 'create');
$rota->get('/sobre', 'HomeController', 'sobre');

$rota->run();
