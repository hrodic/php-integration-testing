<?php declare(strict_types=1);

namespace IntegrationTesting\PHPUnit\Runner\Extension;

use IntegrationTesting\Exception\TestingException;

class PDOFixtureConfig
{
    /**
     * @todo Currently there is a limitation on the extension array index type.
     * It only accepts numeric indexes and not strings, so the XML config is less readable.
     */
    const BEFORE_FIRST_TEST_PDO_FIXTURES_PATH = 'BEFORE_FIRST_TEST_PDO_FIXTURES_PATH';
    const BEFORE_TEST_PDO_FIXTURES_PATH = 'BEFORE_TEST_PDO_FIXTURES_PATH';
    const AFTER_TEST_PDO_FIXTURES_PATH = 'AFTER_TEST_PDO_FIXTURES_PATH';
    const AFTER_LAST_TEST_PDO_FIXTURES_PATH = 'AFTER_LAST_TEST_PDO_FIXTURES_PATH';

    public static $defaultParams = [
        self::BEFORE_FIRST_TEST_PDO_FIXTURES_PATH => '',
        self::BEFORE_TEST_PDO_FIXTURES_PATH => '',
        self::AFTER_TEST_PDO_FIXTURES_PATH => '',
        self::AFTER_LAST_TEST_PDO_FIXTURES_PATH => '',
    ];
    private $params = [];

    /**
     * PDODatabaseExtensionConfig constructor.
     *
     * @param  array $params
     * @throws TestingException
     */
    public function __construct(array $params)
    {
        if (empty($params)) {
            throw new TestingException('Configuration parameters are empty');
        }
        if ($invalidConfigParams = array_diff_key($params, self::$defaultParams)) {
            throw new TestingException(
                'The following elements are not valid configuration params: ' . json_encode($invalidConfigParams)
            );
        }
        $this->params = array_merge(self::$defaultParams, $params);
    }

    /**
     * @param  string $key
     * @return string
     * @throws TestingException
     */
    public function getParam(string $key): string
    {
        if (isset($this->params[$key])) {
            return $this->params[$key];
        }
        throw new TestingException("Parameter [$key] does not exists");
    }
}
