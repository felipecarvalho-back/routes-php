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
        $metodoHttp = $_SERVER['REQUEST_METHOD'];
        $rotaEncontrada = null;

        foreach ($this->routes as $rotaCadastrada => $metodos) {
            $rotaRegex = preg_replace('#/\{[^}]+\}#', '(?:/([a-zA-Z0-9_-]+))?', $rotaCadastrada);
            $regex = '#^' . $rotaRegex . '/?$#';

            if (preg_match($regex, $this->url, $matches)) {
                if (isset($metodos[$metodoHttp])) {

                    $request = null;

                    if ($metodoHttp == 'GET') {
                        $parametroUrl = isset($matches[1]) ? $matches[1] : null;
                        $request = new Request($parametroUrl);
                    }

                    if ($metodoHttp == 'POST') {
                        $request = new Request(null, $_POST);
                    }

                    $rotaEncontrada = $metodos[$metodoHttp];
                    break;
                } else {
                    break;
                }
            }
        }


        if (!$rotaEncontrada) {
            http_response_code(404);
            echo "404 - Rota não encontrada";
            return;
        }

        $arquivo = __DIR__ . "/../controller/{$rotaEncontrada['controller']}.php";

        if (!file_exists($arquivo)) {
            http_response_code(500);
            echo "O arquivo do controller não existe: {$arquivo}";
            return;
        }

        require_once $arquivo;

        $nomeController = $rotaEncontrada['controller'];
        $nomeCompletoDaClasse = "\\Src\\Controller\\" . $nomeController;
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
