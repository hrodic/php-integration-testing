<?php declare(strict_types=1);

namespace IntegrationTesting\PHPUnit\Runner\Extension;

use IntegrationTesting\Exception\TestingException;
use PHPUnit\Framework\TestCase;

/**
 * Class ConfigurationTest
 * @package IntegrationTesting\PHPUnit\Runner\Extension
 * @covers \IntegrationTesting\PHPUnit\Runner\Extension\Configuration
 */
final class ConfigurationTest extends TestCase
{
    public function testConfigurationExceptionWhenNoParams(): void
    {
        $this->expectException(TestingException::class);
        new Configuration([]);
    }
}
