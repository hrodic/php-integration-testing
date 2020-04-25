<?php declare(strict_types=1);

namespace IntegrationTesting\PHPUnit\Runner\Extension;

use IntegrationTesting\Exception\TestingException;

final class Configuration
{
    private const PDO_KEY = 'pdo';
    private const AMQP_KEY = 'amqp';
    private static $defaultPDOParams = [
        'dsn' => 'mysql:host=localhost:3306;dbname=test;charset=utf8',
        'user' => 'test',
        'password' => 'test',
        'fixtures' => [
            'beforeFirstTest' => [],
            'beforeTest' => [],
            'afterTest' => [],
            'afterLastTest' => [],
        ]
    ];
    private static $defaultAMQPParams = [
        'host' => 'rabbitmq',
        'port' => 5672,
        'user' => 'test',
        'password' => 'test',
        'vhost' => 'test',
        'fixtures' => [
            'beforeFirstTest' => [
                'purgeQueues' => [],
                'publishMessages' => []
            ],
            'beforeTest' => [
                'purgeQueues' => [],
                'publishMessages' => []
            ],
            'afterTest' => [
                'purgeQueues' => []
            ],
            'afterLastTest' => [
                'purgeQueues' => []
            ]
        ]
    ];
    private $PDOParams = [];
    private $AMQPParams = [];

    /**
     * PDODatabaseExtensionConfig constructor.
     *
     * @param array $params
     * @throws TestingException
     */
    public function __construct(array $params)
    {
        if (empty($params)) {
            throw new TestingException('Configuration parameters are empty');
        }
        if (isset($params[self::PDO_KEY])) {
            if ($invalidConfigParams = array_diff_key($params[self::PDO_KEY], self::$defaultPDOParams)) {
                throw new TestingException(
                    'The following elements are not valid PDO configuration params: ' . json_encode($invalidConfigParams)
                );
            }
            $this->PDOParams = array_merge(self::$defaultPDOParams, $params[self::PDO_KEY]);
        }
        if (isset($params[self::AMQP_KEY])) {
            if ($invalidConfigParams = array_diff_key($params[self::AMQP_KEY], self::$defaultAMQPParams)) {
                throw new TestingException(
                    'The following elements are not valid AMQP configuration params: ' . json_encode($invalidConfigParams)
                );
            }
            $this->AMQPParams = array_merge(self::$defaultAMQPParams, $params[self::AMQP_KEY]);
        }
    }

    public function getPDODSN(): string
    {
        return $this->PDOParams['dsn'];
    }

    public function getPDOUser(): string
    {
        return $this->PDOParams['user'];
    }

    public function getPDOPassword(): string
    {
        return $this->PDOParams['password'];
    }

    public function getPDOFixtures(): array
    {
        return $this->PDOParams['fixtures'];
    }

    public function getAMQPHost(): string
    {
        return $this->AMQPParams['host'];
    }

    public function getAMQPPort(): int
    {
        return (int)$this->AMQPParams['port'];
    }

    public function getAMQPUser(): string
    {
        return $this->AMQPParams['user'];
    }

    public function getAMQPPassword(): string
    {
        return $this->AMQPParams['password'];
    }

    public function getAMQPVhost(): string
    {
        return $this->AMQPParams['vhost'];
    }

    public function getAMQPFixtures(): array
    {
        return $this->AMQPParams['fixtures'];
    }
}
