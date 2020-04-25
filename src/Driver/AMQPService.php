<?php declare(strict_types=1);

namespace IntegrationTesting\Driver;

use PhpAmqpLib\Channel\AMQPChannel;

/**
 * Interface AMQPService
 * @package IntegrationTesting\Driver
 * @internal NOT FOR PUBLIC USE
 */
interface AMQPService
{
    public function createChannel(): AMQPChannel;

    public function createDurableTopicExchanges(array $exchangeNames): void;

    public function createDurableTopicExchange(string $name, array $arguments = []): AMQPChannel;

    public function forceDeleteExchange(string $name): AMQPChannel;

    public function forceDeleteQueue(string $name): AMQPChannel;

    public function purgeQueue(string $name): AMQPChannel;

    public function disconnect(): void;

    public function createDurableQueue(string $name, array $arguments = []): AMQPChannel;

    public function createDurableQueues(array $queueNames): void;

    public function bindExchangeToQueueByRoutingKeys(string $exchangeName, string $queueName, array $routingKeys): void;

    public function publishMessage(AMQPChannel $channel, string $body, array $properties, string $exchange, string $routingKey): void;

    public function consumeMessage(AMQPChannel $channel, string $consumerTag, string $queue, callable $callback): void;
}
