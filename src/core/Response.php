<?php

declare(strict_types=1);

namespace Src\Core;

class Response
{

    public function redirect(string $url)
    {
        header("Location: $url");
        exit;
    }
}