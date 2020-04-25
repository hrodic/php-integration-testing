<?php declare(strict_types=1);

namespace IntegrationTesting\Tests\Integration;

use IntegrationTesting\Driver\RabbitMQ\RabbitMQService;
use IntegrationTesting\Driver\AMQPConnection;

require_once __DIR__ . '/../../vendor/autoload.php';

/**
 * Class Bootstrap
 * @package IntegrationTesting\Tests\Integration
 * @internal NOT FOR PUBLIC USE
 */
class Bootstrap
{
    public function __construct()
    {
        $rabbitMQService = new RabbitMQService(
            AMQPConnection::create(
                constant('RABBITMQ_HOST'),
                (int)constant('RABBITMQ_PORT'),
                constant('RABBITMQ_USER'),
                constant('RABBITMQ_PASSWORD')
            )
        );
        /**
         * We create here the basic structure of our broker.
         * This is not part of the extension, as each application might have a different way to setup
         * its infrastructure.
         */
        $rabbitMQService->createDurableTopicExchange('test-exchange');
        $rabbitMQService->createDurableQueues(['before-first-test-queue' => [], 'before-test-queue' => []]);
        $rabbitMQService->bindExchangeToQueueByRoutingKeys(
            'test-exchange',
            'before-first-test-queue',
            ['before-first-test']
        );
        $rabbitMQService->bindExchangeToQueueByRoutingKeys(
            'test-exchange',
            'before-test-queue',
            ['before-test']
        );
    }

    public function __invoke()
    {
    }
}

$bootstrap = new Bootstrap();
$bootstrap();
