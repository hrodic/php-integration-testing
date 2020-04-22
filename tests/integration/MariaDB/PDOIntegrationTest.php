<?php declare(strict_types=1);

namespace IntegrationTesting\Tests\Integration;

use IntegrationTesting\Driver\PDOConnection;
use IntegrationTesting\WithAfterTestFixtureName;
use IntegrationTesting\WithBeforeTestFixtureName;
use PHPUnit\Framework\TestCase;

/**
 * @covers \IntegrationTesting\PHPUnit\Runner\Extension\PDODatabaseExtension
 * @uses \IntegrationTesting\Driver\PDOConnection
 */
final class PDOIntegrationTest extends TestCase implements WithBeforeTestFixtureName, WithAfterTestFixtureName
{
    private const FIXTURE_NAME = 'pdo-integration-test';

    public static function getAfterTestFixtureName(): string
    {
        return self::FIXTURE_NAME;
    }

    public static function getBeforeTestFixtureName(): string
    {
        return self::FIXTURE_NAME;
    }

    public function testReadFixtureFromDatabase(): void
    {
        $conn = new PDOConnection(
            constant('DB_DSN'),
            constant('DB_USERNAME'),
            constant('DB_PASSWORD')
        );
        $statement = $conn->PDO()->query("SELECT * FROM  `test`.`ephemeral_table`");
        $data = $statement->fetch();
        $this->assertEquals(
            ['id' => 1, 'float' => '11.62', 'varchar' => 'dummy text', 'datetime' => '2020-04-22 16:25:45'],
            $data
        );
    }
}