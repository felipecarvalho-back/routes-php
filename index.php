<?php

require_once __DIR__ . '/vendor/autoload.php';

use Src\Core\Routes;

$url = $_SERVER['REQUEST_URI'];

$rota = new Routes($url);

$rota->get('/', 'HomeController', 'home');
$rota->get('/home/{text}/{id}', 'HomeController', 'parametro');
$rota->post('/', 'HomeController', 'create');
$rota->get('/sobre', 'HomeController', 'sobre');
$rota->post('/sobre', 'HomeController', 'teste');

$rota->run();
