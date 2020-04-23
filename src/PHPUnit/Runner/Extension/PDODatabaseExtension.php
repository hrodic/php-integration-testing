<?php declare(strict_types=1);

namespace IntegrationTesting\PHPUnit\Runner\Extension;

use IntegrationTesting\Driver\FileSystem;
use IntegrationTesting\Driver\PDOConnection;
use IntegrationTesting\Exception\TestingException;
use PHPUnit\Runner\BeforeFirstTestHook;
use PHPUnit\Runner\BeforeTestHook;
use PHPUnit\Runner\AfterTestHook;
use PHPUnit\Runner\AfterLastTestHook;

final class PDODatabaseExtension implements BeforeFirstTestHook, BeforeTestHook, AfterTestHook, AfterLastTestHook
{
    const EXTENSION_SQL = 'sql';

    private $config;
    private $connection;

    public function __construct(PDODatabaseExtensionConfig $config, PDOConnection $connection)
    {
        $this->config = $config;
        $this->connection = $connection;
    }

    /**
     * @throws TestingException
     */
    public function executeBeforeFirstTest(): void
    {
        $path = $this->config->getParam(PDODatabaseExtensionConfig::BEFORE_FIRST_TEST_PDO_FIXTURES_PATH);
        $this->runFixturesUnderPath($path);
    }

    /**
     * @param string $test
     * @throws TestingException
     */
    public function executeBeforeTest(string $test): void
    {
        $stage = PDODatabaseExtensionConfig::BEFORE_TEST_PDO_FIXTURES_PATH;
        $path = $this->config->getParam($stage);
        $this->runFixturesUnderPath($path);
        $className = '\\' . substr($test, 0, strpos($test, ':'));
        $methodName = 'getBeforeTestFixtureName';
        if (method_exists($className, $methodName)) {
            $fixtureNameFunc = "$className::$methodName";
            $path = $this->config->getParam($stage)
                . DIRECTORY_SEPARATOR
                . $fixtureNameFunc();
            $this->runFixturesUnderPath($path);
        }
    }

    /**
     * @param string $test
     * @param float $time
     * @throws TestingException
     */
    public function executeAfterTest(string $test, float $time): void
    {
        $stage = PDODatabaseExtensionConfig::AFTER_TEST_PDO_FIXTURES_PATH;
        $path = $this->config->getParam($stage);
        $this->runFixturesUnderPath($path);
        $className = '\\' . substr($test, 0, strpos($test, ':'));
        $methodName = 'getAfterTestFixtureName';
        if (method_exists($className, $methodName)) {
            $fixtureNameFunc = "$className::$methodName";
            $path = $this->config->getParam($stage) . DIRECTORY_SEPARATOR . $fixtureNameFunc();
            $this->runFixturesUnderPath($path);
        }
    }

    /**
     * @throws TestingException
     */
    public function executeAfterLastTest(): void
    {
        $path = $this->config->getParam(PDODatabaseExtensionConfig::AFTER_LAST_TEST_PDO_FIXTURES_PATH);
        $this->runFixturesUnderPath($path);
    }

    /**
     * @param string $path
     * @throws TestingException
     */
    public function runFixturesUnderPath(string $path): void
    {
        try {
            $this->connection->PDO()->beginTransaction();
            $iterator = FileSystem::getFileListIteratorFromPathByExtension(
                $path,
                self::EXTENSION_SQL
            );
            FileSystem::runCallbackOnEachFileIteratorContents($iterator, function (string $contents) {
                $this->connection->PDO()->exec($contents);
            });
            $this->connection->PDO()->commit();
        } catch (TestingException $exception) {
            $this->connection->PDO()->rollBack();
            throw $exception;
        }
    }
}
