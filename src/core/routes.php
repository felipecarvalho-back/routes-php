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
        return $this->addRoute('GET', $rota, $controller, $action);
    }

    public function post(string $rota, string $controller, string $action): bool
    {
        return $this->addRoute('POST', $rota, $controller, $action);
    }

    public function addRoute(string $metodo, string $rota, string $controller, string $action): bool
    {
        $rotaNormalizada = trim($rota, '/');

        $this->routes[$rotaNormalizada][$metodo] = [
            'controller' => $controller,
            'action' => strtolower($action),
        ];

        return true;
    }

    public function run(): void
    {
        $metodoHttp = $_SERVER['REQUEST_METHOD'];
        $rotaEncontrada = null;
        $parametroUrl = [];

        foreach ($this->routes as $rotaCadastrada => $metodos) {
            $rotaRegex = preg_replace('/\{([^}]+)\}/', '(?P<$1>[a-zA-Z0-9_-]+)', $rotaCadastrada);

            if (preg_match('#^'.$rotaRegex.'$#', $this->url, $matches)) {
                if (isset($metodos[$metodoHttp])) {
                    $parametroUrl = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                    $rotaEncontrada = $metodos[$metodoHttp];
                    break;
                }
            }
        }

        if (! $rotaEncontrada) {
            http_response_code(404);
            echo "404 - Rota não encontrada";
            return;
        }

        $nomeController = $rotaEncontrada['controller'];
        $nomeCompletoDaClasse = "\\Src\\Controller\\".$nomeController;
        $funcao = $rotaEncontrada['action'];

        try {
            $container = new Container();
            $controller = $container->build($nomeCompletoDaClasse);

            if (! method_exists($controller, $funcao)) {
                http_response_code(500);
                echo "Erro: O método '$funcao' não existe no controller '$nomeController'.";
                return;
            }

            $request = new Request($parametroUrl, $_POST);
            $response = new Response();

            $controller->$funcao($request, $response);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo "Erro ao processar requisição: ".$e->getMessage();
        }
    }
}
