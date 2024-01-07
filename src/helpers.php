<?php

declare(strict_types=1);

use Roave\BetterReflection\BetterReflection;
use Roave\BetterReflection\Reflector\DefaultReflector;
use Roave\BetterReflection\SourceLocator\Type\DirectoriesSourceLocator;

if (!function_exists('find_classes')) {
    function find_classes(... $directories): array
    {
        return (new DefaultReflector(
            new DirectoriesSourceLocator(
                $directories,
                (new BetterReflection())->astLocator()
            )
        ))->reflectAllClasses();
    }
}
