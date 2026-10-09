<?php

declare(strict_types=1);

namespace Src\Core;

class Routes
{
    private string $url = '';
    private array $routes = [];

    public function __construct(string $url)
    {
        $this->url = trim(parse_url($url, PHP_URL_PATH) ?? '', '/');
    }

    public function get(string $rota, string $controller, string $action): bool
    {
        $rotaNormalizada = trim($rota, '/');

        $arquivo = __DIR__ . "/../controller/{$controller}.php";
        $action = strtolower($action);

        if (!file_exists($arquivo)) {
            echo "O arquivo não existe: {$arquivo}";
            return false;
        }

        $this->routes[$rotaNormalizada] = ['controller' => $arquivo, 'action' => $action];
        return true;
    }

    public function run(): void
    {
        print_r($this->routes[$this->url]);
        if (isset($this->routes[$this->url])) {
            // include $this->routes[$this->url];

            // return;
        }

        // http_response_code(404);
        // echo "404 - Rota não encontrada";
    }
}
