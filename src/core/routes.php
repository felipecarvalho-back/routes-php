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

        $this->routes[$rotaNormalizada]['GET'] = ['controller' => $controller, 'action' => $action];
        return true;
    }

    public function post(string $rota, string $controller, string $action): bool
    {
        $rotaNormalizada = trim($rota, '/');

        $arquivo = __DIR__ . "/../controller/{$controller}.php";
        $action = strtolower($action);

        if (!file_exists($arquivo)) {
            echo "O arquivo não existe: {$arquivo}";
            return false;
        }

        $this->routes[$rotaNormalizada]['POST'] = ['controller' => $controller, 'action' => $action];
        return true;
    }

    public function run(): void
    {
        if (!isset($this->routes[$this->url])) {
            http_response_code(404);
            echo "404 - Rota não encontrada";
            return;
        }

        if ($this->routes[$this->url][$_SERVER['REQUEST_METHOD']]) {
            $arquivo = __DIR__ . "/../controller/{$this->routes[$this->url][$_SERVER['REQUEST_METHOD']]['controller']}.php";

            require_once $arquivo;

            $nomeController = $this->routes[$this->url][$_SERVER['REQUEST_METHOD']]['controller'];
            $nomeCompletoDaClasse = "\\Src\\Controller\\" . $nomeController;

            $class = new $nomeCompletoDaClasse();

            if (method_exists($class, $this->routes[$this->url][$_SERVER['REQUEST_METHOD']]['action'])) {
                $funcao = $this->routes[$this->url][$_SERVER['REQUEST_METHOD']]['action'];
                $class->$funcao();
            } else {
                http_response_code(500);
                echo "Erro: O método '$funcao' não existe no controller '$nomeController'.";
            }
        }
    }
}
