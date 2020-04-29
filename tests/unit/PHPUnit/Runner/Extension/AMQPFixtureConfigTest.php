<?php declare(strict_types=1);

namespace IntegrationTesting\PHPUnit\Runner\Extension;

use ArrayIterator;
use IntegrationTesting\Exception\TestingException;
use IntegrationTesting\PHPUnit\Runner\Extension\AMQP\PublishMessageConfig;
use PHPUnit\Framework\TestCase;

/**
 * Class AMQPFixtureConfigTest
 * @package IntegrationTesting\PHPUnit\Runner\Extension
 * @covers \IntegrationTesting\PHPUnit\Runner\Extension\AMQPFixtureConfig
 * @uses   \IntegrationTesting\PHPUnit\Runner\Extension\AMQP\PublishMessageConfig
 */
final class AMQPFixtureConfigTest extends TestCase
{
    public function testConfigurationWithDefaultParams(): void
    {
        $sut = new AMQPFixtureConfig([]);
        $this->assertSame([], iterator_to_array($sut->getMessagesToPublishBeforeFirstTest()));
        $this->assertSame([], iterator_to_array($sut->getMessagesToPublishBeforeTest()));
        $this->assertSame([], iterator_to_array($sut->getQueuesToPurgeBeforeFirstTest()));
        $this->assertSame([], iterator_to_array($sut->getQueuesToPurgeBeforeTest()));
        $this->assertSame([], iterator_to_array($sut->getQueuesToPurgeAfterTest()));
        $this->assertSame([], iterator_to_array($sut->getQueuesToPurgeAfterLastTest()));
    }

    public function testConfigurationExceptionWhenInvalidHookDefinitions(): void
    {
        $this->expectException(TestingException::class);
        new AMQPFixtureConfig([
            'invalidHook' => []
        ]);
    }

    public function testConfigurationExceptionWhenInvalidFileFixtureDefinition(): void
    {
        $this->expectException(TestingException::class);
        new AMQPFixtureConfig([
            'beforeFirstTest' => [
                'invalidKey' => ''
            ]
        ]);
    }

    public function testConfigurationWithCorrectParams(): void
    {
        $sut = new AMQPFixtureConfig([
            'beforeFirstTest' => [
                'publishMessages' => [
                    [
                        'exchange' => 'test-exchange',
                        'queue' => 'test-queue',
                        'path' => '/path'
                    ]
                ],
                'purgeQueues' => ['test']
            ],
            'beforeTest' => [
                'publishMessages' => [
                    [
                        'exchange' => 'test-exchange',
                        'queue' => 'test-queue',
                        'path' => '/path'
                    ]
                ],
                'purgeQueues' => ['test']
            ],
            'afterTest' => [
                'purgeQueues' => ['test']
            ],
            'afterLastTest' => [
                'purgeQueues' => ['test']
            ]
        ]);
        $this->assertInstanceOf(PublishMessageConfig::class, $sut->getMessagesToPublishBeforeFirstTest()->current());
        $this->assertInstanceOf(PublishMessageConfig::class, $sut->getMessagesToPublishBeforeTest()->current());
        $this->assertSame('test', $sut->getQueuesToPurgeBeforeFirstTest()->current());
        $this->assertSame('test', $sut->getQueuesToPurgeBeforeTest()->current());
        $this->assertSame('test', $sut->getQueuesToPurgeAfterTest()->current());
        $this->assertSame('test', $sut->getQueuesToPurgeAfterLastTest()->current());
    }
}
