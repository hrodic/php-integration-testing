<?php declare(strict_types=1);

namespace IntegrationTesting\PHPUnit\Runner\Extension;

use IntegrationTesting\Exception\TestingException;
use PHPUnit\Framework\TestCase;

/**
 * Class ConfigurationTest
 * @covers \IntegrationTesting\PHPUnit\Runner\Extension\Configuration
 */
final class ConfigurationTest extends TestCase
{
    public function testConfigurationExceptionWhenNoParams(): void
    {
        $this->expectException(TestingException::class);
        new Configuration([]);
    }

    public function testConfigurationExceptionWhenInvalidPDOParams(): void
    {
        $this->expectException(TestingException::class);
        new Configuration([
            'pdo' => [
                'invalid'
            ]
        ]);
    }

    public function testConfigurationExceptionWhenInvalidAMQPParams(): void
    {
        $this->expectException(TestingException::class);
        new Configuration([
            'amqp' => [
                'invalid'
            ]
        ]);
    }

    public function testConfigurationWithOnlyPDOParams(): void
    {
        $sut = new Configuration($this->getPDOOnlyJSONConfiguration());
        $this->assertSame('mysql:host=mariadb:3306;dbname=test;charset=utf8', $sut->getPDODSN());
        $this->assertSame('mariadb_user', $sut->getPDOUser());
        $this->assertSame('mariadb_password', $sut->getPDOPassword());
        $this->assertSame([], $sut->getAMQPFixtures());
    }

    public function testConfigurationWithPDOParamsAndDefaultAMQP(): void
    {
        $arrayConfig = array_merge($this->getPDOOnlyJSONConfiguration(), ['amqp' => []]);
        $sut = new Configuration($arrayConfig);
        $this->assertSame('mysql:host=mariadb:3306;dbname=test;charset=utf8', $sut->getPDODSN());
        $this->assertSame('mariadb_user', $sut->getPDOUser());
        $this->assertSame('mariadb_password', $sut->getPDOPassword());
        $this->assertSame([
            'beforeFirstTest' => [],
            'beforeTest' => [],
            'afterTest' => [],
            'afterLastTest' => []
        ], $sut->getAMQPFixtures());
    }

    public function testConfigurationWithOnlyAMQPParams(): void
    {
        $sut = new Configuration($this->getAMQPOnlyJSONConfiguration());
        $this->assertSame('rabbitmq', $sut->getAMQPHost());
        $this->assertSame(5672, $sut->getAMQPPort());
        $this->assertSame('rabbitmq_user', $sut->getAMQPUser());
        $this->assertSame('rabbitmq_password', $sut->getAMQPPassword());
        $this->assertSame('/', $sut->getAMQPVhost());
        $this->assertSame([], $sut->getPDOFixtures());
    }

   public function testConfigurationWithAMQPParamsAndDefaultPDO(): void
    {
        $arrayConfig = array_merge($this->getAMQPOnlyJSONConfiguration(), ['pdo' => []]);
        $sut = new Configuration($arrayConfig);
        $this->assertSame('rabbitmq', $sut->getAMQPHost());
        $this->assertSame(5672, $sut->getAMQPPort());
        $this->assertSame('rabbitmq_user', $sut->getAMQPUser());
        $this->assertSame('rabbitmq_password', $sut->getAMQPPassword());
        $this->assertSame('/', $sut->getAMQPVhost());
        $this->assertSame([
            'beforeFirstTest' => [],
            'beforeTest' => [],
            'afterTest' => [],
            'afterLastTest' => []
        ], $sut->getPDOFixtures());
    }

    private function getPDOOnlyJSONConfiguration(): array
    {
        $json = <<<JSON
{
  "pdo": {
    "dsn": "mysql:host=mariadb:3306;dbname=test;charset=utf8",
    "user": "mariadb_user",
    "password": "mariadb_password",
    "fixtures": {
      "beforeFirstTest": {
        "path": "tests/fixtures/before-first-test",
        "extension": "sql"
      },
      "beforeTest": {
        "path": "tests/fixtures/before-test",
        "extension": "sql"
      },
      "afterTest": {
        "path": "tests/fixtures/after-test"
      },
      "afterLastTest": {
        "path": "tests/fixtures/after-last-test"
      }
    }
  }
}
JSON;
        return json_decode($json, true);
    }

    private function getAMQPOnlyJSONConfiguration(): array
    {
        $json = <<<JSON
{
  "amqp": {
    "host": "rabbitmq",
    "port": 5672,
    "user": "rabbitmq_user",
    "password": "rabbitmq_password",
    "vhost": "/",
    "fixtures": {
      "beforeFirstTest": {
        "purgeQueues": [
          "before-first-test-queue"
        ],
        "publishMessages": [
          {
            "exchange": "test-exchange",
            "queue": "before-first-test-queue",
            "routing_key": "before-first-test",
            "path": "tests/fixtures/before-first-test",
            "extension": "json"
          }
        ]
      },
      "beforeTest": {
        "purgeQueues": [
          "before-test-queue"
        ],
        "publishMessages": [
          {
            "exchange": "test-exchange",
            "queue": "before-test-queue",
            "routing_key": "before-test",
            "path": "tests/fixtures/before-test"
          }
        ]
      },
      "afterTest": {
        "purgeQueues": [
          "before-test-queue"
        ]
      },
      "afterLastTest": {
        "purgeQueues": []
      }
    }
  }
}
JSON;
        return json_decode($json, true);
    }
}
