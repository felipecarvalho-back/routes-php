<?php

declare(strict_types=1);

namespace Src\Model;

use PDO;
use Src\Core\Database;

abstract class Model extends Database
{
    protected string $table = '';
    protected array $atributos = [];
    protected PDO $conn;

    public function __construct()
    {
        $this->conn = Database::conn();
    }

    public function select()
    {
        // Select
    }
}