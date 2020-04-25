<?php declare(strict_types=1);

namespace IntegrationTesting\Tests\Integration\FileSystem;

use IntegrationTesting\Exception\TestingException;
use IntegrationTesting\PHPUnit\Runner\Extension\Handler;
use PHPUnit\Framework\TestCase;

/**
 * Class HandlerConfigurationTest
 * @package IntegrationTesting\Tests\Integration\FileSystem
 * @covers \IntegrationTesting\PHPUnit\Runner\Extension\Handler
 * @uses \IntegrationTesting\Driver\FileSystem
 * @uses \IntegrationTesting\Driver\PDOConnection
 * @uses \IntegrationTesting\PHPUnit\Runner\Extension\Configuration
 * @uses \IntegrationTesting\PHPUnit\Runner\Extension\PDOFixtureConfig
 * @uses \IntegrationTesting\PHPUnit\Runner\Extension\PDOFixtureLoader
 */
final class HandlerConfigurationTest extends TestCase
{
    public function testConstructionOfHandlerWithDefaultConfiguration(): void
    {
        $sut = new Handler();
        $this->assertInstanceOf(Handler::class, $sut);
    }

    public function testConstructorOfHandlerWithCustomConfigurationName(): void
    {
        $this->expectException(TestingException::class);
        new Handler('not-existing-file');
    }
}
