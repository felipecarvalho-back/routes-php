<?php

declare(strict_types=1);

namespace Src\Model;

use Src\Core\Database;

abstract class Model
{
    protected string $table = '';
    protected array $atributos = [];
    private Database $pdo;

    public function __construct()
    {
        $db = new Database();
        $this->pdo = $db;
    }

    public function select()
    {
        var_dump($this->pdo->conn());
    }
}