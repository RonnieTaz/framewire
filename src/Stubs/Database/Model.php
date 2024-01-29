<?php

namespace Framewire\Stubs\Database;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;
use Nette\PhpGenerator\Printer;

readonly class Model
{
    public function __invoke(string $className, ?string $namespace = null): void
    {
        $namespace = is_null($namespace) ? new PhpNamespace('App\\Models') : new PhpNamespace($namespace);
        $class = new ClassType(ucwords($className), $namespace);

        $class->addAttribute(Entity::class);

        $class->addProperty('id')
            ->setType('int')
            ->setPrivate()
            ->addAttribute(Column::class, [
                'type' => 'primary'
            ]);

        $printer = new Printer();

        echo $printer->printClass($class);
    }
}
