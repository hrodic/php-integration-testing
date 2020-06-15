<?php declare(strict_types=1);

namespace IntegrationTesting\PHPUnit\Runner\Extension;

use IntegrationTesting\Driver\AMQPConnection;
use IntegrationTesting\Driver\FileSystem;
use IntegrationTesting\Driver\PDOConnection;
use IntegrationTesting\Driver\RabbitMQ\RabbitMQService;
use IntegrationTesting\Exception\TestingException;
use PHPUnit\Runner\BeforeFirstTestHook;
use PHPUnit\Runner\BeforeTestHook;
use PHPUnit\Runner\AfterTestHook;
use PHPUnit\Runner\AfterLastTestHook;

final class Handler implements BeforeFirstTestHook, BeforeTestHook, AfterTestHook, AfterLastTestHook
{
    /**
     * @var \SplObjectStorage|FixtureLoader[]
     */
    private $fixtureLoaders;
    /**
     * @var FileSystem
     */
    private $fileSystem;

    /**
     * Handler constructor.
     * @param string $configurationFileName
     * @param null $fileSystem
     * @throws TestingException
     */
    public function __construct(string $configurationFileName = '', $fileSystem = null)
    {
        if (null === $fileSystem) {
            $this->fileSystem = new FileSystem();
        }
        $this->fixtureLoaders = new \SplObjectStorage();
        $configuration = $this->getConfigurationFromFileName($configurationFileName);
        $this->initPDOFixtureLoader($configuration);
        $this->initAMQPFixtureLoader($configuration);
    }

    public function executeBeforeFirstTest(): void
    {
        foreach ($this->fixtureLoaders as $fixtureLoader) {
            $fixtureLoader->executeBeforeFirstTest();
        }
    }

    public function executeBeforeTest(string $test): void
    {
        foreach ($this->fixtureLoaders as $fixtureLoader) {
            $fixtureLoader->executeBeforeTest($test);
        }
    }

    public function executeAfterTest(string $test, float $time): void
    {
        foreach ($this->fixtureLoaders as $fixtureLoader) {
            $fixtureLoader->executeAfterTest($test, $time);
        }
    }

    public function executeAfterLastTest(): void
    {
        foreach ($this->fixtureLoaders as $fixtureLoader) {
            $fixtureLoader->executeAfterLastTest();
        }
    }

    /**
     * @param string $fileName
     * @return Configuration
     * @throws TestingException
     */
    private function getConfigurationFromFileName(string $fileName): Configuration
    {
        if (empty($fileName)) {
            $fileName = '.integration-testing.json';
        }
        $data = json_decode($this->fileSystem->getFileContents($fileName), true);
        return new Configuration($data);
    }

    /**
     * @param Configuration $configuration
     * @throws TestingException
     */
    public function initPDOFixtureLoader(Configuration $configuration): void
    {
        if ($configuration->hasPDOFixtures() && $configuration->getPDODSN()) {
            $pdoFixtureConfig = new PDOFixtureConfig($configuration->getPDOFixtures());
            $pdoConnection = new PDOConnection(
                $configuration->getPDODSN(),
                $configuration->getPDOUser(),
                $configuration->getPDOPassword()
            );
            $this->fixtureLoaders->attach(new PDOFixtureLoader($this->fileSystem, $pdoFixtureConfig, $pdoConnection));
        }
    }

    public function initAMQPFixtureLoader(Configuration $configuration): void
    {
        if ($configuration->hasAMQPFixtures() && $configuration->getAMQPFixtures()) {
            $amqpFixtureConfig = new AMQPFixtureConfig($configuration->getAMQPFixtures());
            $amqpConnection = AMQPConnection::create(
                $configuration->getAMQPHost(),
                $configuration->getAMQPPort(),
                $configuration->getAMQPUser(),
                $configuration->getAMQPPassword(),
                $configuration->getAMQPVhost()
            );
            $amqpService = new RabbitMQService($amqpConnection);
            $this->fixtureLoaders->attach(new AMQPFixtureLoader($this->fileSystem, $amqpFixtureConfig, $amqpService));
        }
    }
}
