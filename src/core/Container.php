<?php

declare(strict_types=1);

namespace Src\Core;

use Exception;
use ReflectionClass;
use ReflectionNamedType;

class Container
{
    /**
     * Instancia uma classe resolvendo automaticamente as dependências do construtor.
     */
    public function build(string $className): object
    {
        if (!class_exists($className)) {
            throw new Exception("A classe '{$className}' não existe.");
        }

        $reflector = new ReflectionClass($className);
        $constructor = $reflector->getConstructor();

        if ($constructor === null) {
            return new $className();
        }

        $dependencies = [];

        foreach ($constructor->getParameters() as $parameter) {
            $type = $parameter->getType();

            if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
                $dependencies[] = $this->build($type->getName());
                continue;
            }

            if ($parameter->isDefaultValueAvailable()) {
                $dependencies[] = $parameter->getDefaultValue();
                continue;
            }

            throw new Exception("Não foi possível resolver o parâmetro '\${$parameter->getName()}' em '{$className}'.");
        }

        return $reflector->newInstanceArgs($dependencies);
    }
}
