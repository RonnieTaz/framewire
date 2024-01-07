<?php

namespace App\Http\Controllers;

use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use League\Container\DefinitionContainerInterface;

abstract class Controller implements ContainerAwareInterface
{
    use ContainerAwareTrait;
}
