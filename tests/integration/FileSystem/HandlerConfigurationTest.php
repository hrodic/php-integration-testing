<?php declare(strict_types=1);

namespace IntegrationTesting\Tests\Integration\FileSystem;

use IntegrationTesting\Exception\TestingException;
use IntegrationTesting\PHPUnit\Runner\Extension\Handler;
use PHPUnit\Framework\TestCase;

/**
 * Class HandlerConfigurationTest
 * @package IntegrationTesting\Tests\Integration\FileSystem
 * @coversNothing
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
