<?php

declare(strict_types=1);

namespace Src\Model;

class Usuario extends Model
{
    protected string $table = 'usuarios';
    protected array $atributos = ['nome', 'email'];
}