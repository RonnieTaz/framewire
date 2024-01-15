<?php

declare(strict_types=1);

use Dotenv\Dotenv;
use Framewire\Foundation\App;
use League\Container\Container;
use League\Container\ContainerAwareInterface;
use League\Container\ServiceProvider\ServiceProviderInterface;
use Tracy\Debugger;

require_once dirname(__DIR__) . '/vendor/autoload.php';

Dotenv::createImmutable(dirname(__DIR__))->safeLoad();

Debugger::enable();

$container = new Container();

$container->inflector(ContainerAwareInterface::class)
    ->invokeMethod('setContainer', [$container]);

foreach (find_classes(__DIR__ . '/Providers') as $class) {
    /**
     * @var $class \Roave\BetterReflection\Reflection\ReflectionClass
     */
    $instance = $class->getName();
    $provider = new $instance;
    if ($provider instanceof ServiceProviderInterface) {
        $container->addServiceProvider($provider);
    }
}

/**
 * @var $app App
 */
$container->get(App::class)->run();
