<?php declare(strict_types=1);

namespace IntegrationTesting\PHPUnit\Runner\Extension;

use IntegrationTesting\Driver\FileSystem;
use IntegrationTesting\Driver\PDOConnection;
use IntegrationTesting\Exception\TestingException;

final class PDOFixtureLoader implements FixtureLoader
{
    private $fileSystem;
    private $config;
    private $connection;

    public function __construct(FileSystem $fileSystem, PDOFixtureConfig $config, PDOConnection $connection)
    {
        $this->fileSystem = $fileSystem;
        $this->config = $config;
        $this->connection = $connection;
    }

    /**
     * @throws TestingException
     */
    public function executeBeforeFirstTest(): void
    {
        $params = $this->config->getBeforeFirstTest();
        $this->runFixturesUnderPathWithExtension($params['path'], $params['extension']);
    }

    /**
     * @param string $test
     * @throws TestingException
     */
    public function executeBeforeTest(string $test): void
    {
        $params = $this->config->getBeforeTest();
        $this->runFixturesUnderPathWithExtension($params['path'], $params['extension']);
        $className = '\\' . substr($test, 0, strpos($test, ':'));
        $methodName = 'getBeforeTestFixtureName';
        $this->runSpecificFixturesFromStatic($className, $methodName, $params);
    }

    /**
     * @param string $test
     * @param float $time
     * @throws TestingException
     */
    public function executeAfterTest(string $test, float $time): void
    {
        $params = $this->config->getAfterTest();
        $this->runFixturesUnderPathWithExtension($params['path'], $params['extension']);
        $className = '\\' . substr($test, 0, strpos($test, ':'));
        $methodName = 'getAfterTestFixtureName';
        $this->runSpecificFixturesFromStatic($className, $methodName, $params);
    }

    /**
     * @throws TestingException
     */
    public function executeAfterLastTest(): void
    {
        $params = $this->config->getAfterLastTest();
        $this->runFixturesUnderPathWithExtension($params['path'], $params['extension']);
    }

    /**
     * @param string $path
     * @param string $extension
     * @throws TestingException
     */
    public function runFixturesUnderPathWithExtension(string $path, string $extension): void
    {
        try {
            $this->connection->PDO()->beginTransaction();
            $iterator = $this->fileSystem->getFileListIteratorFromPathByExtension(
                $path,
                $extension
            );
            $this->fileSystem->runCallbackOnEachFileIteratorContents(
                $iterator,
                function (string $contents) {
                    $this->connection->PDO()->exec($contents);
                }
            );
            $this->connection->PDO()->commit();
        } catch (TestingException $exception) {
            $this->connection->PDO()->rollBack();
            throw $exception;
        }
    }

    /**
     * @param string $className
     * @param string $methodName
     * @param array $params
     * @throws TestingException
     */
    public function runSpecificFixturesFromStatic(string $className, string $methodName, array $params): void
    {
        if (method_exists($className, $methodName)) {
            $fixtureNameFunc = "$className::$methodName";
            $this->runFixturesUnderPathWithExtension(
                $params['path'] . DIRECTORY_SEPARATOR . $fixtureNameFunc(),
                $params['extension']
            );
        }
    }
}
