<?php declare(strict_types=1);

namespace IntegrationTesting\PHPUnit\Runner\Extension;

use IntegrationTesting\Exception\TestingException;

class PDOFixtureConfig
{
    private const BEFORE_FIRST_TEST_KEY = 'beforeFirstTest';
    private const BEFORE_TEST_KEY = 'beforeTest';
    private const AFTER_TEST_KEY = 'afterTest';
    private const AFTER_LAST_TEST_KEY = 'afterLastTest';
    private const DEFAULT_EXTENSION = 'sql';

    public static $defaultParams = [
        self::BEFORE_FIRST_TEST_KEY => [
            'path' => '',
            'extension' => self::DEFAULT_EXTENSION
        ],
        self::BEFORE_TEST_KEY => [
            'path' => '',
            'extension' => self::DEFAULT_EXTENSION
        ],
        self::AFTER_TEST_KEY => [
            'path' => '',
            'extension' => self::DEFAULT_EXTENSION
        ],
        self::AFTER_LAST_TEST_KEY => [
            'path' => '',
            'extension' => self::DEFAULT_EXTENSION
        ]
    ];
    private $params = [];

    /**
     * PDOFixtureConfig constructor.
     * @param array $params
     * @throws TestingException
     */
    public function __construct(array $params)
    {
        if ($invalidConfigParams = array_diff_key($params, self::$defaultParams)) {
            throw new TestingException(
                'The following elements are not valid PDO configuration params: ' . json_encode($invalidConfigParams)
            );
        }
        $this->params = array_merge(self::$defaultParams, $params);
        foreach ($this->params as $key => $value) {
            if (isset($params[$key])) {
                if ($invalidConfigParams = array_diff_key($params[$key], self::$defaultParams[$key])) {
                    throw new TestingException(
                        'The following elements are not valid PDO configuration params: ' . json_encode($invalidConfigParams)
                    );
                }
                $this->params[$key] = array_merge(self::$defaultParams[$key], $params[$key]);
            }
        }
    }

    /**
     * @return array['path' => 'extension']
     */
    public function getBeforeFirstTest(): array
    {
        return $this->params[self::BEFORE_FIRST_TEST_KEY];
    }

    public function getBeforeTest(): array
    {
        return $this->params[self::BEFORE_TEST_KEY];
    }

    public function getAfterTest(): array
    {
        return $this->params[self::AFTER_TEST_KEY];
    }

    public function getAfterLastTest(): array
    {
        return $this->params[self::AFTER_LAST_TEST_KEY];
    }
}
