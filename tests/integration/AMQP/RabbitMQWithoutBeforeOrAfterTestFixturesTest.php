<?php declare(strict_types=1);

namespace IntegrationTesting\Tests\Integration\AMQP;

use IntegrationTesting\Driver\AMQPConnection;
use IntegrationTesting\Driver\AMQPService;
use IntegrationTesting\Driver\RabbitMQ\RabbitMQService;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Exception\AMQPTimeoutException;
use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\TestCase;

/**
 * Class RabbitMQWithoutBeforeOrAfterTestFixturesTest
 * @package IntegrationTesting\Tests\Integration\AMQP
 * @coversNothing
 * @group debug
 */
final class RabbitMQWithoutBeforeOrAfterTestFixturesTest extends TestCase
{
    /**
     * @var AMQPService
     */
    private $rabbitMQService;
    /**
     * @var AMQPChannel
     */
    private $channel;

    public function setUp(): void
    {
        $this->rabbitMQService = new RabbitMQService(
            AMQPConnection::create(
                constant('RABBITMQ_HOST'),
                (int)constant('RABBITMQ_PORT'),
                constant('RABBITMQ_USER'),
                constant('RABBITMQ_PASSWORD')
            )
        );
        $this->channel = $this->rabbitMQService->createChannel();
    }

    /**
     * @throws \ErrorException
     */
    public function testConsumingMessageAddedBeforeFirstTest(): void
    {
        $messageBody = null;
        $consumerTag = uniqid();
        $callback = function (AMQPMessage $message) use (&$messageBody, $consumerTag) {
            /**
             * We consumed the before-first-test message, but nack requeued it. so still be there.
             */
            $this->channel->basic_nack($message->getDeliveryTag(), false, true);
            $this->channel->basic_cancel($consumerTag);
            $messageBody = $message->getBody();
        };
        $this->rabbitMQService->consumeMessage($this->channel, $consumerTag, 'before-first-test-queue', $callback, 1);
        $this->assertSame(['hook' => 'before-first-test'], json_decode($messageBody, true));
    }

    /**
     * @throws \ErrorException
     */
    public function testConsumingMessageAddedBeforeTest(): void
    {
        $messageBody = null;
        $consumerTag = uniqid();
        $callback = function (AMQPMessage $message) use (&$messageBody, $consumerTag) {
            $this->channel->basic_ack($message->getDeliveryTag());
            $this->channel->basic_cancel($consumerTag);
            $messageBody = $message->getBody();
        };
        $this->rabbitMQService->consumeMessage($this->channel, $consumerTag, 'before-test-queue', $callback, 1);
        $this->assertSame(['hook' => 'before-test'], json_decode($messageBody, true));
    }

    /**
     * @throws \ErrorException
     */
    public function testThatICannotConsumeMoreThanOneMessageSinceAllArePurgedAndOnlyOnePublishedPerTest(): void
    {
        $consumerTag = uniqid();
        $callback = function (AMQPMessage $message) {
            $this->channel->basic_ack($message->getDeliveryTag());
        };
        $callbackEnd = function () use ($consumerTag) {
        };
        $this->expectException(AMQPTimeoutException::class);
        $this->rabbitMQService->consumeMessage($this->channel, $consumerTag, 'before-test-queue', $callback, 1);
        $this->rabbitMQService->consumeMessage($this->channel, $consumerTag, 'before-test-queue', $callbackEnd, 1);
    }
}
