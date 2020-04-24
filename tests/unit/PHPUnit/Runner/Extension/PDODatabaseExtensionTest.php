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
final class PDODatabaseExtensionTest extends TestCase
{
    public function testBeforeFirstTestBehaviour(): void
    {
        $PDO = $this->createMock(PDO::class);
        $config = $this->createMock(PDOFixtureConfig::class);
        $connection = $this->createMock(PDOConnection::class);

        $config->expects($this->once())
            ->method('getParam')
            ->with(PDOFixtureConfig::BEFORE_FIRST_TEST_PDO_FIXTURES_PATH);

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
            ->method('getParam')
            ->with(PDOFixtureConfig::BEFORE_TEST_PDO_FIXTURES_PATH);

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
            ->method('getParam')
            ->with(PDOFixtureConfig::AFTER_TEST_PDO_FIXTURES_PATH);

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
            ->method('getParam')
            ->with(PDOFixtureConfig::AFTER_LAST_TEST_PDO_FIXTURES_PATH);

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
