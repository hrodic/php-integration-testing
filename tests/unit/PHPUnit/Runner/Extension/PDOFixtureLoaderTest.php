<?php declare(strict_types=1);

namespace IntegrationTesting\PHPUnit\Runner\Extension;

use IntegrationTesting\Driver\FileSystem;
use IntegrationTesting\Driver\PDOConnection;
use PDO;
use PHPUnit\Framework\TestCase;

/**
 * @covers \IntegrationTesting\PHPUnit\Runner\Extension\PDOFixtureLoader
 * @uses   \IntegrationTesting\Driver\FileSystem
 */
final class PDOFixtureLoaderTest extends TestCase
{
    public function testBeforeFirstTestBehaviour(): void
    {
        $PDO = $this->createMock(PDO::class);
        $config = $this->createMock(PDOFixtureConfig::class);
        $connection = $this->createMock(PDOConnection::class);

        $config->expects($this->once())
            ->method('getBeforeFirstTest')
            ->willReturn(['path' => '/dir', 'extension' => 'sql']);

        $PDO->expects($this->once())
            ->method('beginTransaction');
        $PDO->expects($this->never())
            ->method('exec');
        $PDO->expects($this->once())
            ->method('commit');

        $connection->expects($this->exactly(2))
            ->method('PDO')
            ->willReturn($PDO);

        $fileSystem = $this->createMock(FileSystem::class);

        $sut = new PDOFixtureLoader($fileSystem, $config, $connection);
        $sut->executeBeforeFirstTest();
    }

    public function testBeforeTestBehaviour(): void
    {
        $PDO = $this->createMock(PDO::class);
        $config = $this->createMock(PDOFixtureConfig::class);
        $connection = $this->createMock(PDOConnection::class);

        $config->expects($this->once())
            ->method('getBeforeTest')
            ->willReturn(['path' => '/dir', 'extension' => 'sql']);

        $PDO->expects($this->once())
            ->method('beginTransaction');
        $PDO->expects($this->never())
            ->method('exec');
        $PDO->expects($this->once())
            ->method('commit');

        $connection->expects($this->exactly(2))
            ->method('PDO')
            ->willReturn($PDO);

        $fileSystem = $this->createMock(FileSystem::class);

        $sut = new PDOFixtureLoader($fileSystem, $config, $connection);
        $sut->executeBeforeTest(__METHOD__);
    }

    public function testAfterTestBehaviour(): void
    {
        $PDO = $this->createMock(PDO::class);
        $config = $this->createMock(PDOFixtureConfig::class);
        $connection = $this->createMock(PDOConnection::class);

        $config->expects($this->once())
            ->method('getAfterTest')
            ->willReturn(['path' => '/dir', 'extension' => 'sql']);

        $PDO->expects($this->once())
            ->method('beginTransaction');
        $PDO->expects($this->never())
            ->method('exec');
        $PDO->expects($this->once())
            ->method('commit');

        $connection->expects($this->exactly(2))
            ->method('PDO')
            ->willReturn($PDO);

        $fileSystem = $this->createMock(FileSystem::class);

        $sut = new PDOFixtureLoader($fileSystem, $config, $connection);
        $sut->executeAfterTest(__METHOD__, floatval(1));
    }

    public function testAfterLastTestBehaviour(): void
    {
        $PDO = $this->createMock(PDO::class);
        $config = $this->createMock(PDOFixtureConfig::class);
        $connection = $this->createMock(PDOConnection::class);

        $config->expects($this->once())
            ->method('getAfterLastTest')
            ->willReturn(['path' => '/dir', 'extension' => 'sql']);

        $PDO->expects($this->once())
            ->method('beginTransaction');
        $PDO->expects($this->never())
            ->method('exec');
        $PDO->expects($this->once())
            ->method('commit');

        $connection->expects($this->exactly(2))
            ->method('PDO')
            ->willReturn($PDO);

        $fileSystem = $this->createMock(FileSystem::class);

        $sut = new PDOFixtureLoader($fileSystem, $config, $connection);
        $sut->executeAfterLastTest();
    }
}
