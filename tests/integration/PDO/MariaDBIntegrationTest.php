<?php declare(strict_types=1);

namespace IntegrationTesting\Tests\Integration\PDO;

use IntegrationTesting\Driver\PDOConnection;
use IntegrationTesting\WithAfterTestFixtureName;
use IntegrationTesting\WithBeforeTestFixtureName;
use PHPUnit\Framework\TestCase;

/**
 * Class MariaDBIntegrationTest
 * @package IntegrationTesting\Tests\Integration\PDO
 * @coversNothing
 */
final class MariaDBIntegrationTest extends TestCase implements WithBeforeTestFixtureName, WithAfterTestFixtureName
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

    public function testPersistentTableHasAtLeastOneRow(): void
    {
        $conn = new PDOConnection(
            constant('DB_DSN'),
            constant('DB_USERNAME'),
            constant('DB_PASSWORD')
        );
        $conn->PDO()->beginTransaction();
        $statement = $conn->PDO()->query("SELECT * FROM  `test`.`persistent_table`");
        $conn->PDO()->commit();
        // this is the third test to run a beforeTest hook!
        $this->assertTrue($statement->rowCount() >= 1);
    }
}
