<?php

namespace Framewire\Providers;

use Framewire\Foundation\Logs\Loggers\AggregateLogger;
use League\Container\ServiceProvider\AbstractServiceProvider;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

class LoggingServiceProvider extends AbstractServiceProvider
{

    public function provides(string $id): bool
    {
        return in_array($id, [
            LoggerInterface::class,
            AggregateLogger::class
        ]);
    }

    public function register(): void
    {
        $this->getContainer()->add(LoggerInterface::class, Logger::class)->addArgument('base');
        $this->getContainer()->add(AggregateLogger::class);
    }
}
