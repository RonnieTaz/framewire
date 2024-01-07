<?php

namespace Framewire\Foundation\Logs\Loggers;

use Framewire\Foundation\Exceptions\InvalidLoggerChannelException;
use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Monolog\Processor\ProcessorInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;
use Framewire\Enum\Logger as LoggerType;

class AggregateLogger implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    private array $loggers = [
        LoggerType::EVENT->value => [],
        LoggerType::HTTP->value => [],
    ];

    public function __construct()
    {
        $this->loggers[LoggerType::EVENT->value]['handlers'][] = new StreamHandler(
            dirname(__DIR__, 4) . '/storage/logs/events.log',
            Level::Info
        );
        $this->loggers[LoggerType::HTTP->value]['handlers'][] = new StreamHandler(dirname(__DIR__, 4) . '/storage/logs/http.log', Level::Info);

    }


    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidLoggerChannelException
     */
    public function getLogger(LoggerType $channel): LoggerInterface
    {
        if (!array_key_exists($channel->value, $this->loggers)) {
            throw InvalidLoggerChannelException::reason(sprintf('The provided channel %s is not defined.', $channel->value));
        }
        /**
         * @var $logger Logger
         */
        $logger = $this->getContainer()->get(LoggerInterface::class)->withName($channel->value);

        if (array_key_exists('handlers', $this->loggers[$channel->value])) {
            array_walk(
                $this->loggers[$channel->value]['handlers'],
                fn (HandlerInterface $handler) => $logger->pushHandler($handler)
            );
        }
        if (array_key_exists('processors', $this->loggers[$channel->value])) {
            array_walk(
                $this->loggers[$channel->value]['processors'],
                fn (callable|ProcessorInterface $processor) => $logger->pushProcessor($processor)
            );
        }

        return $logger;
    }
}
