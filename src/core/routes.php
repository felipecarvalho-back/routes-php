<?php

declare(strict_types=1);

namespace Src\Core;

class Routes
{
    private string $url = '';
    private array $routes = [];

    public function __construct()
    {
        $this->url = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '', '/');
    }

    public function get(string $rota, string $controller, string $action): bool
    {
        if ($this->addRoute('GET', $rota, $controller, $action)) {
            return true;
        };

        return false;
    }

    public function post(string $rota, string $controller, string $action): bool
    {
        if ($this->addRoute('POST', $rota, $controller, $action)) {
            return true;
        };

        return false;
    }

    public function addRoute(string $metodo, string $rota, string $controller, string $action): bool
    {
        $rotaNormalizada = trim($rota, '/');

        $arquivo = __DIR__ . "/../controller/{$controller}.php";
        $action = strtolower($action);

        if (!file_exists($arquivo)) {
            echo "O arquivo não existe: {$arquivo}";
            return false;
        }

        $this->routes[$rotaNormalizada][$metodo] = ['controller' => $controller, 'action' => $action];
        return true;
    }

    public function run(): void
    {
        $metodoHttp = $_SERVER['REQUEST_METHOD'];
        $rotaEncontrada = null;
        $request = '';

        foreach ($this->routes as $rotaCadastrada => $metodos) {
            $rotaRegex = preg_replace('#/\{[^}]+\}#', '(?:/([a-zA-Z0-9_-]+))?', $rotaCadastrada);
            $regex = '#^' . $rotaRegex . '/?$#';
            if (preg_match($regex, $this->url, $matches)) {
                if (isset($metodos[$metodoHttp])) {
                    preg_match_all('/{([^}]+)}/', $rotaCadastrada, $indices);
                    unset($indices[0]);
                    $indices = array_values(array_merge(...$indices));

                    unset($matches[0]);
                    $matches = array_values($matches);

                    $parametroUrl = array_combine($indices, $matches);

                    $request = new Request($parametroUrl, $_POST);
                    $rotaEncontrada = $metodos[$metodoHttp];
                }
            }
        }

        if (!$rotaEncontrada) {
            http_response_code(404);
            echo "404 - Rota não encontrada";
            return;
        }

        $nomeController = $rotaEncontrada['controller'];
        $nomeCompletoDaClasse = "\\Src\\Controller\\" . $nomeController;

        if (!class_exists($nomeCompletoDaClasse)) {
            http_response_code(500);
            echo "Controller '$nomeCompletoDaClasse' não encontrado.";
            return;
        }

        $class = new $nomeCompletoDaClasse();

        $funcao = $rotaEncontrada['action'];

        if (method_exists($class, $funcao)) {
            $class->$funcao($request);
        } else {
            http_response_code(500);
            echo "Erro: O método '$funcao' não existe no controller '$nomeController'.";
        }
    }
}
