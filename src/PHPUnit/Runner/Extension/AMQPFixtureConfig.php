<?php declare(strict_types=1);

namespace IntegrationTesting\PHPUnit\Runner\Extension;

use IntegrationTesting\Exception\TestingException;
use IntegrationTesting\PHPUnit\Runner\Extension\AMQP\PublishMessageConfig;
use Iterator;

class AMQPFixtureConfig
{
    private const BEFORE_FIRST_TEST_KEY = 'beforeFirstTest';
    private const BEFORE_TEST_KEY = 'beforeTest';
    private const AFTER_TEST_KEY = 'afterTest';
    private const AFTER_LAST_TEST_KEY = 'afterLastTest';
    private const PURGE_QUEUES_KEY = 'purgeQueues';
    private const PUBLISH_MESSAGES_KEY = 'publishMessages';
    private static $defaultParams = [
        self::BEFORE_FIRST_TEST_KEY => [
            self::PUBLISH_MESSAGES_KEY => [],
            self::PURGE_QUEUES_KEY => []
        ],
        self::BEFORE_TEST_KEY => [
            self::PUBLISH_MESSAGES_KEY => [],
            self::PURGE_QUEUES_KEY => []
        ],
        self::AFTER_TEST_KEY => [
            self::PURGE_QUEUES_KEY => []
        ],
        self::AFTER_LAST_TEST_KEY => [
            self::PURGE_QUEUES_KEY => []
        ]
    ];
    private $params = [];

    public function __construct(array $params)
    {
        if ($invalidConfigParams = array_diff_key($params, self::$defaultParams)) {
            throw new TestingException(
                'The following elements are not valid AMQP configuration params: ' . json_encode($invalidConfigParams)
            );
        }
        $this->params = array_merge(self::$defaultParams, $params);
        foreach ($this->params as $key => $value) {
            if (isset($params[$key])) {
                if ($invalidConfigParams = array_diff_key($params[$key], self::$defaultParams[$key])) {
                    throw new TestingException(
                        'The following elements are not valid AMQP configuration params: ' . json_encode($invalidConfigParams)
                    );
                }
                $this->params[$key] = array_merge(self::$defaultParams[$key], $params[$key]);
            }
        }
    }

    /**
     * @return Iterator|PublishMessageConfig[]
     */
    public function getMessagesToPublishBeforeFirstTest(): Iterator
    {
        $elements = new \ArrayIterator();
        foreach ($this->params[self::BEFORE_FIRST_TEST_KEY][self::PUBLISH_MESSAGES_KEY] as $params) {
            $elements->append(new PublishMessageConfig($params));
        }
        return $elements;
    }

    public function getQueuesToPurgeBeforeFirstTest(): Iterator
    {
        return new \ArrayIterator($this->params[self::BEFORE_FIRST_TEST_KEY][self::PURGE_QUEUES_KEY]);
    }

    public function getMessagesToPublishBeforeTest(): Iterator
    {
        $elements = new \ArrayIterator();
        foreach ($this->params[self::BEFORE_TEST_KEY][self::PUBLISH_MESSAGES_KEY] as $params) {
            $elements->append(new PublishMessageConfig($params));
        }
        return $elements;
    }

    public function getQueuesToPurgeBeforeTest(): Iterator
    {
        return new \ArrayIterator($this->params[self::BEFORE_TEST_KEY][self::PURGE_QUEUES_KEY]);
    }

    public function getQueuesToPurgeAfterTest(): Iterator
    {
        return new \ArrayIterator($this->params[self::AFTER_TEST_KEY][self::PURGE_QUEUES_KEY]);
    }

    public function getQueuesToPurgeAfterLastTest(): Iterator
    {
        return new \ArrayIterator($this->params[self::AFTER_LAST_TEST_KEY][self::PURGE_QUEUES_KEY]);
    }
}
