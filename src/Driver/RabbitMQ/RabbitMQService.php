<?php declare(strict_types=1);

namespace IntegrationTesting\Driver\RabbitMQ;

use Exception;
use IntegrationTesting\Driver\AMQPConnection;
use IntegrationTesting\Driver\AMQPService;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Class RabbitMQService
 * @package IntegrationTesting\Driver\RabbitMQ
 * @internal NOT FOR PUBLIC USE
 */
class RabbitMQService implements AMQPService
{
    /**
     * @var AMQPConnection
     */
    private $connection;

    public function __construct(AMQPConnection $connection)
    {
        $this->connection = $connection;
    }

    public function createChannel(): AMQPChannel
    {
        $channel = $this->connection->getChannel();
        $channel->basic_qos(null, 1, null);
        return $channel;
    }

    public function createDurableTopicExchanges(array $exchangeNames): void
    {
        foreach ($exchangeNames as $exchangeName => $options) {
            $arguments = $options['arguments'] ?? [];
            $this->createDurableTopicExchange($exchangeName, $arguments);
        }
    }

    public function createDurableTopicExchange(string $name, array $arguments = []): AMQPChannel
    {
        $channel = $this->createChannel();
        $channel->exchange_declare($name, 'topic', false, true, false, false, false, $arguments);

        return $channel;
    }

    public function forceDeleteExchange(string $name): AMQPChannel
    {
        $channel = $this->createChannel();
        $channel->exchange_delete($name);

        return $channel;
    }

    public function forceDeleteQueue(string $name): AMQPChannel
    {
        $channel = $this->createChannel();
        $channel->queue_delete($name);

        return $channel;
    }

    public function purgeQueue(string $name): AMQPChannel
    {
        $channel = $this->createChannel();
        $channel->queue_purge($name);
        return $channel;
    }

    public function disconnect(): void
    {
        $this->connection->disconnect();
    }

    public function createDurableQueue(string $name, array $arguments = []): AMQPChannel
    {
        $channel = $this->createChannel();
        $channel->queue_declare($name, false, true, false, false, false, $arguments);

        return $channel;
    }

    public function createDurableQueues(array $queueNames): void
    {
        foreach ($queueNames as $queueName => $options) {
            $arguments = $options['arguments'] ?? [];
            $this->createDurableQueue($queueName, $arguments);
        }
    }

    /**
     * @param string $exchangeName
     * @param string $queueName
     * @param array $routingKeys
     * @throws Exception
     */
    public function bindExchangeToQueueByRoutingKeys(string $exchangeName, string $queueName, array $routingKeys): void
    {
        $channel = $this->createChannel();
        try {
            foreach ($routingKeys as $routingKey) {
                $channel->queue_bind($queueName, $exchangeName, $routingKey);
            }
        } catch (Exception $exception) {
            throw new Exception(sprintf(
                'failed to bind exchange %s to queue %s by routing keys %s',
                $exchangeName,
                $queueName,
                json_encode($routingKeys)
            ), 0, $exception);
        }
    }

    /**
     * @param AMQPChannel $channel
     * @param string $body
     * @param array $properties
     * @param string $exchange
     * @param string $routingKey
     */
    public function publishMessage(AMQPChannel $channel, string $body, array $properties, string $exchange, string $routingKey): void
    {
        $channel->basic_publish(
            new AMQPMessage($body, $properties),
            $exchange,
            $routingKey,
            true
        );
    }

    /**
     * @param AMQPChannel $channel
     * @param string $consumerTag
     * @param string $queue
     * @param callable $callback
     * @param int $timeout
     * @throws \ErrorException
     */
    public function consumeMessage(AMQPChannel $channel, string $consumerTag, string $queue, callable $callback, int $timeout = 0): void
    {
        $channel->basic_consume($queue, $consumerTag, false, false, false, false, $callback);
        while (count($channel->callbacks)) {
            $channel->wait(null, false, $timeout);
        }
    }
}
