<?php

declare(strict_types=1);

namespace Src\controller;


abstract class Controller{
    protected function view(string $view){
        $arquivo = __DIR__ . "/../view/$view.php";

        if(!file_exists($arquivo)){
            http_response_code(500);
            echo "Erro: O arquivo '$arquivo' não existe.";
        }

        include_once $arquivo;
    }
}