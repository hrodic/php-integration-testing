<?php declare(strict_types=1);

namespace IntegrationTesting\PHPUnit\Runner\Extension;

use IntegrationTesting\Driver\FileSystem;
use IntegrationTesting\Driver\PDOConnection;
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

    public function __construct(string $configurationFileName = '', $fileSystem = null)
    {
        if (null === $fileSystem) {
            $this->fileSystem = new FileSystem();
        }
        $this->fixtureLoaders = new \SplObjectStorage();
        $configuration = $this->getConfigurationFromFileName($configurationFileName);
        if ($configuration->getPDODSN()) {
            $pdoFixtureConfig = new PDOFixtureConfig([
                PDOFixtureConfig::BEFORE_FIRST_TEST_PDO_FIXTURES_PATH =>
                    $configuration->getPDOFixtures()['beforeFirstTest']['path'],
                PDOFixtureConfig::BEFORE_TEST_PDO_FIXTURES_PATH =>
                    $configuration->getPDOFixtures()['beforeTest']['path'],
                PDOFixtureConfig::AFTER_TEST_PDO_FIXTURES_PATH =>
                    $configuration->getPDOFixtures()['afterTest']['path'],
                PDOFixtureConfig::AFTER_LAST_TEST_PDO_FIXTURES_PATH =>
                    $configuration->getPDOFixtures()['afterLastTest']['path']
            ]);
            $pdoConnection = new PDOConnection(
                $configuration->getPDODSN(),
                $configuration->getPDOUser(),
                $configuration->getPDOPassword()
            );
            $this->fixtureLoaders->attach(new PDOFixtureLoader($this->fileSystem, $pdoFixtureConfig, $pdoConnection));
        }
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
}
