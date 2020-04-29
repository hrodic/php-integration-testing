<?php declare(strict_types=1);

namespace IntegrationTesting\PHPUnit\Runner\Extension;

use IntegrationTesting\Exception\TestingException;
use PHPUnit\Framework\TestCase;

/**
 * Class ConfigurationTest
 * @covers \IntegrationTesting\PHPUnit\Runner\Extension\PDOFixtureConfig
 */
final class PDOFixtureConfigTest extends TestCase
{
    public function testConfigurationWithDefaultParams(): void
    {
        $sut = new PDOFixtureConfig([]);
        $this->assertSame(['path' => '', 'extension' => 'sql'], $sut->getBeforeFirstTest());
        $this->assertSame(['path' => '', 'extension' => 'sql'], $sut->getBeforeTest());
        $this->assertSame(['path' => '', 'extension' => 'sql'], $sut->getAfterTest());
        $this->assertSame(['path' => '', 'extension' => 'sql'], $sut->getAfterLastTest());
    }

    public function testConfigurationExceptionWhenInvalidHookDefinitions(): void
    {
        $this->expectException(TestingException::class);
        new PDOFixtureConfig([
            'invalidHook' => []
        ]);
    }

    public function testConfigurationExceptionWhenInvalidFileFixtureDefinition(): void
    {
        $this->expectException(TestingException::class);
        new PDOFixtureConfig([
            'beforeFirstTest' => [
                'path' => '/path',
                'invalidKey' => ''
            ]
        ]);
    }

    public function testConfigurationWithCorrectParams(): void
    {
        $sut = new PDOFixtureConfig([
            'beforeFirstTest' => [
                'path' => '/path',
                'extension' => 'invented'
            ],
            'beforeTest' => [
                'path' => '/path2'
            ],
            'afterTest' => [
                'path' => '/path3'
            ],
            'afterLastTest' => [
                'path' => '/path4'
            ]
        ]);
        $this->assertSame(['path' => '/path', 'extension' => 'invented'], $sut->getBeforeFirstTest());
        $this->assertSame(['path' => '/path2', 'extension' => 'sql'], $sut->getBeforeTest());
        $this->assertSame(['path' => '/path3', 'extension' => 'sql'], $sut->getAfterTest());
        $this->assertSame(['path' => '/path4', 'extension' => 'sql'], $sut->getAfterLastTest());
    }
}
