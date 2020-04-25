<?php declare(strict_types=1);

namespace IntegrationTesting\Driver;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;

/**
 * Class AMQPConnection
 * @package IntegrationTesting\Driver
 * @internal NOT FOR PUBLIC USE
 */
class AMQPConnection
{
    /**
     * @var AMQPStreamConnection
     */
    private $connection;

    private function __construct(
        AMQPStreamConnection $connection
    ) {
        $this->connection = $connection;
        $this->connection->set_close_on_destruct(true);
    }

    public static function create(
        string $host,
        int $port,
        string $user,
        string $password,
        string $vHost = '/',
        bool $insist = false,
        string $loginMethod = 'AMQPLAIN',
        string $locale = 'en_US',
        float $connectionTimeout = 3.0,
        float $readWriteTimeout = 3.0,
        bool $keepAlive = false,
        int $heartbeat = 0
    ): self {
        return new self(
            new AMQPStreamConnection(
                $host,
                $port,
                $user,
                $password,
                $vHost,
                $insist,
                $loginMethod,
                null,
                $locale,
                $connectionTimeout,
                $readWriteTimeout,
                null,
                $keepAlive,
                $heartbeat
            )
        );
    }

    public function connect(): void
    {
        if (!$this->connection->isConnected()) {
            $this->connection->reconnect();
        }
    }

    public function disconnect(): void
    {
        if ($this->connection->isConnected()) {
            $this->connection->close();
        }
    }

    /**
     * @param int $channelId
     * @return AMQPChannel
     */
    public function getChannel(int $channelId = null): AMQPChannel
    {
        return $this->connection->channel($channelId);
    }
}
