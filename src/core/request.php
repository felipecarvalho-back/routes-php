<?php

declare(strict_types=1);

namespace Src\Core;

class Request
{

    private mixed $parametro = null;
    private array $posts = [];

    public function __construct(mixed $parametro = null, array $posts = [])
    {
        $this->parametro = $parametro;
        $this->posts = $posts;
    }

    public function param(): mixed
    {
        return $this->parametro;
    }

    public function post(): array
    {
        $dados = [];
        foreach ($this->posts as $rotulo => $post) {
            $dados[$rotulo] = $post;
        }

        return $dados;
    }
}
