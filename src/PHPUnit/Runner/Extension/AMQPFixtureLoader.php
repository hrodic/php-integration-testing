<?php declare(strict_types=1);

namespace IntegrationTesting\PHPUnit\Runner\Extension;

use IntegrationTesting\Driver\FileSystem;
use IntegrationTesting\Driver\AMQPService;
use IntegrationTesting\Exception\TestingException;
use IntegrationTesting\PHPUnit\Runner\Extension\AMQP\PublishMessageConfig;
use PhpAmqpLib\Message\AMQPMessage;

final class AMQPFixtureLoader implements FixtureLoader
{
    private $fileSystem;
    private $config;
    private $service;
    private $channel;

    public function __construct(FileSystem $fileSystem, AMQPFixtureConfig $config, AMQPService $service)
    {
        $this->fileSystem = $fileSystem;
        $this->config = $config;
        $this->service = $service;
        $this->channel = $this->service->createChannel();
        $this->channel->confirm_select();
    }

    /**
     * Purge occurs before publishing
     * @throws TestingException
     */
    public function executeBeforeFirstTest(): void
    {
        $queuesToPurge = $this->config->getQueuesToPurgeBeforeFirstTest();
        foreach ($queuesToPurge as $queue) {
            $this->purgeQueue($queue);
        }
        $messagesToPublish = $this->config->getMessagesToPublishBeforeFirstTest();
        foreach ($messagesToPublish as $message) {
            $this->publishMessage($message);
        }
    }

    /**
     * Purge occurs before publishing
     * @param string $test
     * @throws TestingException
     */
    public function executeBeforeTest(string $test): void
    {
        $queuesToPurge = $this->config->getQueuesToPurgeBeforeTest();
        foreach ($queuesToPurge as $queue) {
            $this->purgeQueue($queue);
        }
        $messagesToPublish = $this->config->getMessagesToPublishBeforeTest();
        foreach ($messagesToPublish as $message) {
            $this->publishMessage($message);
        }
    }

    public function executeAfterTest(string $test, float $time): void
    {
        $queuesToPurge = $this->config->getQueuesToPurgeAfterTest();
        foreach ($queuesToPurge as $queue) {
            $this->purgeQueue($queue);
        }
    }

    public function executeAfterLastTest(): void
    {
        $queuesToPurge = $this->config->getQueuesToPurgeAfterLastTest();
        foreach ($queuesToPurge as $queue) {
            $this->purgeQueue($queue);
        }
    }

    /**
     * @param PublishMessageConfig $publishMessageConfig
     * @throws TestingException
     */
    public function publishMessage(PublishMessageConfig $publishMessageConfig): void
    {
        $iterator = $this->fileSystem->getFileListIteratorFromPathByExtension(
            $publishMessageConfig->getPath(),
            $publishMessageConfig->getExtension()
        );
        $this->fileSystem->runCallbackOnEachFileIteratorContents(
            $iterator,
            function (string $body) use ($publishMessageConfig) {
                $this->service->publishMessage(
                    $this->channel,
                    $body,
                    [
                        'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
                        'message_id' => uniqid()
                    ],
                    $publishMessageConfig->getExchange(),
                    $publishMessageConfig->getRoutingKey()
                );
                $this->channel->wait_for_pending_acks_returns(1);
            }
        );
    }

    public function purgeQueue(string $queueName): void
    {
        $this->service->purgeQueue($queueName);
    }
}
