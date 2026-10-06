<?php

declare(strict_types=1);

namespace Src\Core;

class routes
{
    private string $url = '';
    private array $routes = [];
    public function __construct(string $url)
    {
        $this->url = $url;
    }

    public function get(string $rota)
    {
        echo "$rota";
    }
}
