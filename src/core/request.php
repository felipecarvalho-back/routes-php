<?php

declare(strict_types=1);

namespace Src\Core;

class Request
{

    private mixed $parametros = [];
    private array $posts = [];

    public function __construct(array $parametros = [], array $posts = [])
    {
        $this->parametros = $parametros;
        $this->posts = $posts;
    }

    public function param(?string $campo = null): mixed
    {
        if ($campo === null) {
            return $this->parametros;
        }
        return $this->parametros[$campo] ?? null;
    }

    public function post(?string $campo = null): mixed
    {
        if ($campo === null) {
            return $this->posts;
        }
        return $this->posts[$campo] ?? null;
    }
}
