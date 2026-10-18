<?php

require_once(dirname(__DIR__).'../vendor/autoload.php');

use Src\Core\Routes;

error_reporting(E_ALL);
ini_set('display_errors', true);

$rota = new Routes();

require_once dirname(__DIR__) . '/src/routes/rotas.php';

$rota->run();
