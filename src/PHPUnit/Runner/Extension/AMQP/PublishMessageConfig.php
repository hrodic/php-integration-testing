<?php declare(strict_types=1);

namespace IntegrationTesting\PHPUnit\Runner\Extension\AMQP;

class PublishMessageConfig
{
    private const EXCHANGE_KEY = 'exchange';
    private const QUEUE_KEY = 'queue';
    private const ROUTING_KEY_KEY = 'routing_key';
    private const PATH_KEY = 'path';
    private const EXTENSION_KEY = 'extension';
    private const DEFAULT_EXTENSION = 'json';
    private static $defaultParams = [
        self::EXCHANGE_KEY => '',
        self::QUEUE_KEY => '',
        self::ROUTING_KEY_KEY => '',
        self::PATH_KEY => '',
        self::EXTENSION_KEY => self::DEFAULT_EXTENSION
    ];
    private $params;

    public function __construct(array $params)
    {
        $this->params = array_merge(self::$defaultParams, $params);
    }

    public function getExchange(): string
    {
        return $this->params[self::EXCHANGE_KEY];
    }

    public function getQueue(): string
    {
        return $this->params[self::QUEUE_KEY];
    }

    public function getRoutingKey(): string
    {
        return $this->params[self::ROUTING_KEY_KEY];
    }

    public function getPath(): string
    {
        return $this->params[self::PATH_KEY];
    }

    public function getExtension(): string
    {
        return $this->params[self::EXTENSION_KEY];
    }
}
